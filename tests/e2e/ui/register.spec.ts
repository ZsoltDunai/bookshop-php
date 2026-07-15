import { test } from "@helpers/fixtures";

test.describe("Register UI", () => {
  test("creates a new account", async ({ registerPage }) => {
    const email = `user-${Date.now()}@bookshop.io`;

    await registerPage.register(email);
    await registerPage.expectRegisteredAs(email);
  });

  test("shows error for duplicate email", async ({ registerPage }) => {
    const email = `dup-${Date.now()}@bookshop.io`;

    await registerPage.register(email);
    await registerPage.expectRegisteredAs(email);

    await registerPage.logout();
    await registerPage.register(email);
    await registerPage.expectRegisterError();
  });
});
