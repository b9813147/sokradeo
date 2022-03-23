/**
 * This is a test suite for user.js
 * Language: javascript
 * Path: __tests__\services\user.test.js
 * Author: Nuttaphat Arunoprayoch (@nuttaphat)
 * Updated: 21-JAN-2022
 * Documenation: http://192.168.1.2:10000/legend/sokradeo/wiki/Docs%3A+Testing
 */
import UserService from "../../resources/services/user";

const userService = UserService;

test("Test User Service instance", () => {
  expect(userService).toBeTruthy();
});

test("Test User Service APU urls", () => {
  expect(userService.getApiUrl()).toContain("/user/");
  expect(userService.getTbaVideoApiUrl()).toContain("/user/tbavideo/");
});
