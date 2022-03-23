/**
 * Test Suites for content.js
 * Language: javascript
 * Path: __tests__\services\content.test.js
 * Author: Nuttaphat Arunoprayoch (@nuttaphat)
 * Updated: 08-FEB-2022
 * Documenation: http://192.168.1.2:10000/legend/sokradeo/wiki/Docs%3A+Testing
 */

import ContentService from "../../resources/services/content";

const service = ContentService;

test("Test Content Service instance", () => {
  expect(service).toBeTruthy();
});

test("Test Content Service API Tba url", () => {
  expect(service.getApiTbaUrl()).toContain("/tbas");
});
