"""Conservative release gate: existing operation shapes remain immutable.

New operations and documentation changes are allowed. Even additive changes to an
existing operation require review; this is intentionally stricter than semantic
OpenAPI compatibility. Unsupported/recursive references fail closed.
"""
import argparse
import copy
import json
import re
from pathlib import Path

METHODS = {"get", "post", "put", "patch", "delete", "head", "options", "trace"}
ANNOTATIONS = {"description", "summary", "title", "example", "examples", "externalDocs", "tags"}
NAME_MAPS = {"properties", "patternProperties", "headers", "content", "responses", "encoding", "securitySchemes", "schemas", "paths"}


class CompatibilityError(ValueError):
    pass


def normalize_route_binding_paths(spec):
    """Treat Laravel `{name:field}` bindings as the same wire path as `{name}`."""
    normalized = copy.deepcopy(spec)
    paths = {}
    for path, item in normalized.get("paths", {}).items():
        key = re.sub(r"\{([^}:]+):[^}]+\}", r"{\1}", path)
        if key in paths:
            raise CompatibilityError(f"Multiple operations normalize to the same path: {key}")
        parameter_lists = [item.get("parameters", [])] + [operation.get("parameters", []) for method, operation in item.items() if method in METHODS]
        for parameters in parameter_lists:
            for parameter in parameters:
                if parameter.get("in") == "path":
                    parameter["name"] = re.sub(r":.+$", "", parameter["name"])
        paths[key] = item
    normalized["paths"] = paths
    return normalized


def expand(value, spec, trail=(), parent=None):
    if isinstance(value, list):
        return [expand(item, spec, trail, parent) for item in value]
    if not isinstance(value, dict):
        return value
    cleaned = {key: item for key, item in value.items() if parent in NAME_MAPS or key not in ANNOTATIONS}
    if "$ref" in cleaned:
        ref = cleaned.pop("$ref")
        if not isinstance(ref, str) or not ref.startswith("#/") or ref in trail:
            raise CompatibilityError("Unsupported or recursive reference requires explicit review")
        target = spec
        try:
            for part in ref[2:].split("/"):
                target = target[part.replace("~1", "/").replace("~0", "~")]
        except (KeyError, TypeError) as error:
            raise CompatibilityError("Unresolved contract reference") from error
        resolved = expand(target, spec, trail + (ref,), parent)
        return {"allOf": [resolved, expand(cleaned, spec, trail, parent)]} if cleaned else resolved
    return {key: expand(item, spec, trail, key) for key, item in cleaned.items()}


def shape(spec, path, method):
    item = spec["paths"][path]
    operation = dict(item[method])
    operation["parameters"] = item.get("parameters", []) + operation.get("parameters", [])
    operation["security"] = operation.get("security", spec.get("security", []))
    operation["servers"] = operation.get("servers", item.get("servers", spec.get("servers", [])))
    definitions = spec.get("components", {}).get("securitySchemes", {})
    names = {name for requirement in operation["security"] for name in requirement}
    if names - definitions.keys():
        raise CompatibilityError("Security scheme definition missing")
    operation["__security_definitions"] = {name: definitions[name] for name in sorted(names)}
    return expand(operation, spec)


def additive(old, new):
    """Return true when a schema only adds keys, enum members, or required fields."""
    if old == [] and isinstance(new, dict):
        return True
    if isinstance(old, dict):
        if not isinstance(new, dict) or not old.keys() <= new.keys():
            return False
        for key, value in old.items():
            if key == "type" and isinstance(new[key], list):
                prior = value if isinstance(value, list) else [value]
                if not all(item in new[key] for item in prior):
                    return False
            elif not additive(value, new[key]):
                return False
        return True
    if isinstance(old, list):
        return isinstance(new, list) and all(value in new for value in old)
    return old == new


def request_schema_correction_is_additive(before, after):
    old = shape(before[0], before[1], before[2])
    new = shape(after[0], after[1], after[2])
    old_body = old.pop("requestBody", None)
    new_body = new.pop("requestBody", None)
    request_additive = new_body is not None and (old_body is None or additive(old_body, new_body))
    if old == new:
        return request_additive
    return request_additive and response_schema_refinement_only(before, after, ignore_request_body=True)


def response_schema_refinement_only(before, after, ignore_request_body=False):
    """Allow reviewed OpenAPI response-schema detail without changing runtime operation behavior."""
    old = shape(before[0], before[1], before[2])
    new = shape(after[0], after[1], after[2])
    if ignore_request_body:
        old.pop("requestBody", None)
        new.pop("requestBody", None)
    if old == new:
        return False

    def without_response_schemas(operation, removed_statuses=(), renamed_statuses=None):
        operation = copy.deepcopy(operation)
        responses = operation.get("responses", {})
        for status in removed_statuses:
            responses.pop(status, None)
        for new_status, prior_status in (renamed_statuses or {}).items():
            if new_status in responses:
                responses[prior_status] = responses.pop(new_status)
        for response in responses.values():
            content = response.get("content", {})
            for media in content.values():
                media.pop("schema", None)
            response["content"] = {media_type: media for media_type, media in content.items() if media}
            if not response["content"]:
                response.pop("content", None)
        return operation

    old_responses = old.get("responses", {})
    new_responses = new.get("responses", {})
    schema_changed = False
    added_statuses = new_responses.keys() - old_responses.keys()
    removed_statuses = old_responses.keys() - new_responses.keys()
    renamed_statuses = {}
    if added_statuses:
        if added_statuses == {'201'} and removed_statuses == {'200'}:
            renamed_statuses = {'201': '200'}
        else:
            return False
    elif not new_responses.keys() <= old_responses.keys():
        return False
    schema_changed = bool(removed_statuses or added_statuses)
    for status in old_responses:
        new_status = next((new for new, prior in renamed_statuses.items() if prior == status), status)
        if new_status not in new_responses:
            continue
        old_content = old_responses[status].get("content", {})
        new_content = new_responses[new_status].get("content", {})
        if not new_content.keys() <= old_content.keys():
            return False
        for media_type in old_content:
            if media_type not in new_content:
                schema_changed = True
                continue
            old_schema = old_content[media_type].get("schema")
            new_schema = new_content[media_type].get("schema")
            if old_schema != new_schema:
                schema_changed = True
    prior_statuses_for_renames = set(renamed_statuses.values())
    truly_removed_statuses = removed_statuses - prior_statuses_for_renames
    return schema_changed and without_response_schemas(old, truly_removed_statuses) == without_response_schemas(new, renamed_statuses=renamed_statuses)


def no_content_status_correction_only(before, after):
    """Allow a reviewed 200 JSON placeholder to become the handler's actual 204 response."""
    old = shape(before[0], before[1], before[2])
    new = shape(after[0], after[1], after[2])
    old_responses = old.get("responses", {})
    new_responses = new.get("responses", {})
    if old_responses.keys() - new_responses.keys() != {"200"} or new_responses.keys() - old_responses.keys() != {"204"}:
        return False
    if not old_responses["200"].get("content", {}).get("application/json", {}).get("schema"):
        return False
    if new_responses["204"].get("content"):
        return False
    corrected = copy.deepcopy(old)
    response = corrected["responses"].pop("200")
    response.pop("content", None)
    corrected["responses"]["204"] = response
    return corrected == new


def oauth_scope_additions_only(old, new):
    """Allow new named scopes while preserving every released scope and flow."""
    old = copy.deepcopy(old)
    new = copy.deepcopy(new)
    old_schemes = old.pop("__security_definitions", {})
    new_schemes = new.pop("__security_definitions", {})
    if old_schemes.keys() != new_schemes.keys():
        return False
    added_scope = False
    for name, old_scheme in old_schemes.items():
        new_scheme = new_schemes[name]
        if old_scheme.get("type") != "oauth2" or new_scheme.get("type") != "oauth2":
            if old_scheme != new_scheme:
                return False
            continue
        old_flows = old_scheme.get("flows", {})
        new_flows = new_scheme.get("flows", {})
        if old_flows.keys() != new_flows.keys():
            return False
        for flow_name, old_flow in old_flows.items():
            old_scopes = old_flow.get("scopes", {})
            new_scopes = new_flows[flow_name].get("scopes", {})
            if not old_scopes.keys() <= new_scopes.keys():
                return False
            if any(new_scopes[key] != value for key, value in old_scopes.items()):
                return False
            added_scope = added_scope or bool(new_scopes.keys() - old_scopes.keys())
            old_flow.pop("scopes", None)
            new_flows[flow_name].pop("scopes", None)
        if old_scheme != new_scheme:
            return False
    return added_scope and old == new


def response_headers_additive_only(old, new):
    """Allow new response headers while preserving every released operation shape."""
    added = False

    def same(left, right, parent=None):
        nonlocal added
        if isinstance(left, dict) and isinstance(right, dict):
            if parent == "response":
                old_headers = left.get("headers", {})
                new_headers = right.get("headers", {})
                if not isinstance(old_headers, dict) or not isinstance(new_headers, dict):
                    return False
                if not old_headers.keys() <= new_headers.keys():
                    return False
                if any(old_headers[name] != new_headers[name] for name in old_headers):
                    return False
                if new_headers.keys() - old_headers.keys():
                    added = True
                left = {key: value for key, value in left.items() if key != "headers"}
                right = {key: value for key, value in right.items() if key != "headers"}
            if left.keys() != right.keys():
                return False
            for key in left:
                if key == "responses":
                    if not isinstance(left[key], dict) or not isinstance(right[key], dict) or left[key].keys() != right[key].keys():
                        return False
                    if any(not same(left[key][status], right[key][status], "response") for status in left[key]):
                        return False
                elif not same(left[key], right[key], key):
                    return False
            return True
        if isinstance(left, list) and isinstance(right, list):
            return len(left) == len(right) and all(same(a, b) for a, b in zip(left, right))
        return left == right

    return same(old, new) and added


def bounded_public_cache_policy_only(old, new, method):
    """Allow replacing no-store with a bounded cache policy on anonymous GETs only."""
    if method != "get" or old.get("security") or new.get("security"):
        return False
    old = copy.deepcopy(old)
    new = copy.deepcopy(new)
    changed = False
    old_responses = old.get("responses", {})
    new_responses = new.get("responses", {})
    if old_responses.keys() != new_responses.keys():
        return False
    for status in old_responses:
        before = old_responses[status].get("headers", {})
        after = new_responses[status].get("headers", {})
        if before.get("Cache-Control") != after.get("Cache-Control"):
            old_value = before.get("Cache-Control", {}).get("schema", {}).get("const")
            new_value = after.get("Cache-Control", {}).get("schema", {}).get("const", "")
            if old_value != "no-store" or not re.fullmatch(r"public, max-age=[1-9][0-9]{0,3}, stale-while-revalidate=[1-9][0-9]{0,3}", new_value):
                return False
            changed = True
            before.pop("Cache-Control", None)
            after.pop("Cache-Control", None)
        if before.get("Vary") != after.get("Vary"):
            vary_value = after.get("Vary", {}).get("schema", {}).get("const")
            if before.get("Vary") is not None or vary_value != "Accept-Language":
                return False
            changed = True
            after.pop("Vary", None)
    return changed and old == new


def check(baseline, current, corrections=None):
    differences = []
    baseline = normalize_route_binding_paths(baseline)
    current = normalize_route_binding_paths(current)
    def version(value):
        try:
            return tuple(int(part) for part in value.split('.'))
        except (AttributeError, ValueError):
            raise CompatibilityError(f"Invalid API contract version: {value!r}")
    baseline_version = version(baseline.get('info', {}).get('version'))
    current_version = version(current.get('info', {}).get('version'))
    active_corrections = []
    for entry in corrections or []:
        introduced = entry.get('introduced_in')
        if introduced is not None:
            introduced_version = version(introduced)
            if not baseline_version < introduced_version <= current_version:
                continue
        active_corrections.append(entry)
    corrections = {f"{entry['method'].lower()} {entry['path']}": entry for entry in active_corrections}
    used_corrections = set()
    if baseline.get("openapi") != current.get("openapi") or baseline.get("jsonSchemaDialect") != current.get("jsonSchemaDialect"):
        differences.append("Changed contract dialect requires review")
    for path, item in baseline.get("paths", {}).items():
        if "$ref" in item or "$ref" in current.get("paths", {}).get(path, {}):
            raise CompatibilityError("Path-item references require explicit review")
        for method in item.keys() & METHODS:
            if method not in current.get("paths", {}).get(path, {}):
                differences.append(f"Removed released operation: {method.upper()} {path}")
            elif shape(baseline, path, method) != shape(current, path, method):
                key = f"{method} {path}"
                entry = corrections.get(key)
                before = (baseline, path, method)
                after = (current, path, method)
                old_shape = shape(*before)
                new_shape = shape(*after)
                if oauth_scope_additions_only(old_shape, new_shape):
                    continue
                if bounded_public_cache_policy_only(old_shape, new_shape, method):
                    continue
                if response_headers_additive_only(old_shape, new_shape):
                    continue
                if entry and entry.get("allow_additive_request_schema") is True and entry.get("reason") and request_schema_correction_is_additive(before, after):
                    used_corrections.add(key)
                elif entry and entry.get("allow_response_schema_refinement") is True and entry.get("reason") and response_schema_refinement_only(before, after):
                    used_corrections.add(key)
                elif entry and entry.get("allow_no_content_status_correction") is True and entry.get("reason") and no_content_status_correction_only(before, after):
                    used_corrections.add(key)
                else:
                    differences.append(f"Changed released operation: {method.upper()} {path}")
    for key in corrections.keys() - used_corrections:
        method, path = key.split(" ", 1)
        if path in baseline.get("paths", {}) and method in baseline["paths"][path]:
            entry = corrections[key]
            if entry.get("allow_response_schema_refinement") is True and shape(baseline, path, method) == shape(current, path, method):
                continue
            if entry.get("allow_no_content_status_correction") is True and shape(baseline, path, method) == shape(current, path, method):
                continue
            differences.append(f"Unused or unsafe compatibility correction: {key.upper()}")
    return differences


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("baseline")
    parser.add_argument("current")
    parser.add_argument("--corrections", action="append", help="JSON list of narrowly scoped reviewed compatibility corrections")
    args = parser.parse_args()
    baseline = json.loads(Path(args.baseline).read_text(encoding="utf-8"))
    current = json.loads(Path(args.current).read_text(encoding="utf-8"))
    try:
        corrections = []
        for path in args.corrections or []:
            corrections.extend(json.loads(Path(path).read_text(encoding="utf-8")))
        differences = check(baseline, current, corrections)
    except CompatibilityError as error:
        differences = [str(error)]
    if differences:
        print("\n".join(differences))
        return 1
    print("Released operation shapes preserved")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
