"""Conservative release gate: existing operation shapes remain immutable.

New operations and documentation changes are allowed. Even additive changes to an
existing operation require review; this is intentionally stricter than semantic
OpenAPI compatibility. Unsupported/recursive references fail closed.
"""
import argparse
import json
from pathlib import Path

METHODS = {"get", "post", "put", "patch", "delete", "head", "options", "trace"}
ANNOTATIONS = {"description", "summary", "title", "example", "examples", "externalDocs", "tags"}
NAME_MAPS = {"properties", "patternProperties", "headers", "content", "responses", "encoding", "securitySchemes", "schemas", "paths"}


class CompatibilityError(ValueError):
    pass


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


def check(baseline, current):
    differences = []
    if baseline.get("openapi") != current.get("openapi") or baseline.get("jsonSchemaDialect") != current.get("jsonSchemaDialect"):
        differences.append("Changed contract dialect requires review")
    for path, item in baseline.get("paths", {}).items():
        if "$ref" in item or "$ref" in current.get("paths", {}).get(path, {}):
            raise CompatibilityError("Path-item references require explicit review")
        for method in item.keys() & METHODS:
            if method not in current.get("paths", {}).get(path, {}):
                differences.append(f"Removed released operation: {method.upper()} {path}")
            elif shape(baseline, path, method) != shape(current, path, method):
                differences.append(f"Changed released operation: {method.upper()} {path}")
    return differences


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("baseline")
    parser.add_argument("current")
    args = parser.parse_args()
    baseline = json.loads(Path(args.baseline).read_text(encoding="utf-8"))
    current = json.loads(Path(args.current).read_text(encoding="utf-8"))
    try:
        differences = check(baseline, current)
    except CompatibilityError as error:
        differences = [str(error)]
    if differences:
        print("\n".join(differences))
        return 1
    print("Released operation shapes preserved")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
