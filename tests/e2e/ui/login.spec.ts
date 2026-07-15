import { test } from "@helpers/fixtures";
import { DEMO_USER } from "@helpers/constants";

test.describe("Login UI", () => {
  test("home page links to login", async ({ loginPage }) => {
    await loginPage.openFromHome();
  });

  test("successful login redirects home", async ({ loginPage, shopPage }) => {
    await loginPage.login();
    await shopPage.expectLoaded();
    await loginPage.expectLoggedInAs(DEMO_USER.email);
  });

  test("invalid login shows error", async ({ loginPage }) => {
    await loginPage.login(DEMO_USER.email, "wrong-password");
    await loginPage.expectLoginError();
  });
});
