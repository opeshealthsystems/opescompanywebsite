import copy
import json
from pathlib import Path
import unittest

from check_compatibility import check, CompatibilityError


class CompatibilityTests(unittest.TestCase):
    def setUp(self):
        root = Path(__file__).resolve().parents[2]
        self.baseline = json.loads((root / "docs/api/baselines/0.3.0.json").read_text(encoding="utf-8"))
        self.current = copy.deepcopy(self.baseline)

    def test_identical_and_documentation_only_changes_pass(self):
        self.current["info"]["version"] = "0.3.1"
        self.current["paths"]["/enquiries"]["post"]["summary"] = "Updated documentation"
        self.assertEqual([], check(self.baseline, self.current))

    def test_new_operation_is_allowed(self):
        self.current["paths"]["/new-resource"] = {"get": {"responses": {"200": {"description": "Example"}}}}
        self.assertEqual([], check(self.baseline, self.current))

    def test_removed_operation_is_rejected(self):
        del self.current["paths"]["/enquiries"]
        self.assertTrue(check(self.baseline, self.current))

    def test_required_input_change_is_rejected_through_reference(self):
        self.current["components"]["schemas"]["EnquiryInput"]["required"].append("phone")
        self.assertTrue(check(self.baseline, self.current))

    def test_response_field_removal_is_rejected(self):
        del self.current["components"]["schemas"]["CatalogueProduct"]["properties"]["name"]
        self.assertTrue(check(self.baseline, self.current))

    def test_business_field_named_title_is_not_treated_as_documentation(self):
        del self.current["components"]["schemas"]["Problem"]["properties"]["title"]
        self.assertTrue(check(self.baseline, self.current))

    def test_added_scope_requirement_is_rejected(self):
        self.current["paths"]["/integration"]["get"]["security"][0]["OAuth2"].append("catalogue:read")
        self.assertTrue(check(self.baseline, self.current))

    def test_new_oauth_scope_definition_is_backward_compatible(self):
        scopes = self.current["components"]["securitySchemes"]["OAuth2"]["flows"]["clientCredentials"]["scopes"]
        scopes["publishing:read"] = "Read published business content"
        self.assertEqual([], check(self.baseline, self.current))

    def test_removing_a_released_oauth_scope_is_rejected(self):
        scopes = self.current["components"]["securitySchemes"]["OAuth2"]["flows"]["clientCredentials"]["scopes"]
        del scopes["catalogue:read"]
        self.assertTrue(check(self.baseline, self.current))

    def test_token_url_or_base_url_change_is_rejected(self):
        self.current["components"]["securitySchemes"]["OAuth2"]["flows"]["clientCredentials"]["tokenUrl"] = "/other/token"
        self.assertTrue(check(self.baseline, self.current))
        self.current = copy.deepcopy(self.baseline)
        self.current["servers"][0]["url"] = "/api/v2"
        self.assertTrue(check(self.baseline, self.current))

    def test_external_reference_requires_review(self):
        self.current["components"]["schemas"]["EnquiryInput"] = {"$ref": "https://example.test/schema.json"}
        with self.assertRaises(CompatibilityError):
            check(self.baseline, self.current)

    def test_contract_dialect_change_requires_review(self):
        self.current["openapi"] = "3.2.0"
        self.assertTrue(check(self.baseline, self.current))


if __name__ == "__main__":
    unittest.main()
