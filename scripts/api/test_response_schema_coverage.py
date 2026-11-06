import json
import unittest
from pathlib import Path

from report_response_schema_coverage import coverage, has_fields


class ResponseSchemaCoverageTest(unittest.TestCase):
    def test_resolves_referenced_fields_and_ignores_open_record_only_schemas(self):
        components = {
            "Payload": {"type": "object", "properties": {"id": {"type": "integer"}}},
        }
        self.assertTrue(has_fields({"$ref": "#/components/schemas/Payload"}, components))
        self.assertFalse(has_fields({"type": "object", "additionalProperties": True}, components))

    def test_handles_composed_schemas_and_recursive_refs(self):
        components = {
            "Payload": {"allOf": [{"$ref": "#/components/schemas/Recursive"}]},
            "Recursive": {"oneOf": [{"$ref": "#/components/schemas/Recursive"}, {"type": "object", "properties": {"ok": {"type": "boolean"}}}]},
        }
        self.assertTrue(has_fields({"$ref": "#/components/schemas/Payload"}, components))

    def test_current_contract_response_schema_baseline(self):
        root = Path(__file__).resolve().parents[2]
        contract = json.loads((root / "docs/api/openapi.json").read_text(encoding="utf-8"))
        counts, _ = coverage(contract)
        self.assertEqual(counts, {
            "operations": 656,
            "field_specific": 205,
            "generic": 448,
            "no_content": 3,
            "missing_schema": 0,
        })


if __name__ == "__main__":
    unittest.main()
