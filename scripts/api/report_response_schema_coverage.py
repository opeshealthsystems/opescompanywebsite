#!/usr/bin/env python3
"""Report how specifically each OpenAPI operation describes a success payload."""

from __future__ import annotations

import argparse
import json
from pathlib import Path
from typing import Any


def resolve(schema: dict[str, Any], components: dict[str, Any]) -> dict[str, Any]:
    seen: set[str] = set()
    while "$ref" in schema:
        ref = schema["$ref"]
        if not ref.startswith("#/components/schemas/") or ref in seen:
            return {}
        seen.add(ref)
        schema = components.get(ref.rsplit("/", 1)[-1], {})
    return schema if isinstance(schema, dict) else {}


def has_fields(schema: dict[str, Any], components: dict[str, Any], seen: set[str] | None = None) -> bool:
    seen = seen or set()
    ref = schema.get("$ref")
    if ref:
        if ref in seen:
            return False
        seen.add(ref)
        return has_fields(resolve(schema, components), components, seen)
    if schema.get("properties"):
        return True
    return any(
        has_fields(part, components, seen.copy())
        for keyword in ("allOf", "oneOf", "anyOf")
        for part in schema.get(keyword, [])
        if isinstance(part, dict)
    )


def coverage(contract: dict[str, Any]) -> tuple[dict[str, int], list[str]]:
    components = contract.get("components", {}).get("schemas", {})
    counts = {"operations": 0, "field_specific": 0, "generic": 0, "no_content": 0, "missing_schema": 0}
    gaps: list[str] = []
    for path_item in contract.get("paths", {}).values():
        for operation in path_item.values():
            if not isinstance(operation, dict) or not operation.get("operationId"):
                continue
            counts["operations"] += 1
            schemas: list[dict[str, Any]] = []
            no_content = False
            for status, response in operation.get("responses", {}).items():
                if not str(status).startswith("2") or not isinstance(response, dict):
                    continue
                no_content = no_content or str(status) == "204"
                for media in response.get("content", {}).values():
                    if isinstance(media, dict) and isinstance(media.get("schema"), dict):
                        schemas.append(media["schema"])

            useful = False
            for schema in schemas:
                root = resolve(schema, components)
                data = root.get("properties", {}).get("data")
                if isinstance(data, dict) and has_fields(data, components):
                    useful = True
                    break
            if useful:
                counts["field_specific"] += 1
            elif schemas:
                counts["generic"] += 1
                gaps.append(operation["operationId"])
            elif no_content:
                counts["no_content"] += 1
            else:
                counts["missing_schema"] += 1
                gaps.append(operation["operationId"])
    return counts, sorted(gaps)


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("contract", nargs="?", default="docs/api/openapi.json")
    parser.add_argument("--list-gaps", action="store_true", help="include operation IDs whose data schema has no declared fields")
    parser.add_argument("--minimum-field-specific", type=int, default=0, help="fail if fewer operations have field-specific data schemas")
    parser.add_argument("--max-missing-schemas", type=int, default=0, help="fail if more operations lack a success schema")
    args = parser.parse_args()
    contract = json.loads(Path(args.contract).read_text(encoding="utf-8"))
    counts, gaps = coverage(contract)
    result: dict[str, Any] = counts
    if args.list_gaps:
        result["generic_or_missing_operations"] = sorted(gaps)
    print(json.dumps(result, indent=2, sort_keys=True))
    return 1 if counts["field_specific"] < args.minimum_field_specific or counts["missing_schema"] > args.max_missing_schemas else 0


if __name__ == "__main__":
    raise SystemExit(main())
