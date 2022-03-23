/**
 * This is a test suite for my-sokrates.js
 * Language: javascript
 * Path: __tests__\services\my-sokrates.test.js
 * Author: Nuttaphat Arunoprayoch (@nuttaphat)
 * Updated: 27-JAN-2022
 * Documenation: http://192.168.1.2:10000/legend/sokradeo/wiki/Docs%3A+Testing
 */
import MySokratesService from "../../resources/services/my-sokrates";

const service = MySokratesService;

test("Test My Sokrates Service instance", () => {
  expect(service).toBeTruthy();
});

test("Test My Sokrates Service API urls", () => {
  expect(service.getApiUrl()).toContain("/exhibition/");
  expect(service.getTbaVideoApiUrl()).toContain("/tbavideo/");
});
