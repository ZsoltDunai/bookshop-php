import { Page, expect } from "@playwright/test";
import { DEMO_USER } from "../constants";

export class LoginPage {
  constructor(private readonly page: Page) {}

  async goto() {
    await this.page.goto("/login");
  }

  async login(email = DEMO_USER.email, password = DEMO_USER.password) {
    await this.goto();
    await this.page.getByTestId("login-email").fill(email);
    await this.page.getByTestId("login-password").fill(password);
    await this.page.getByTestId("login-submit").click();
    await Promise.race([
      this.page.waitForURL("/", { timeout: 15_000 }),
      this.page.getByTestId("login-alert").waitFor({ state: "visible", timeout: 15_000 }),
    ]);
  }

  async expectLoginError() {
    await expect(this.page.getByTestId("login-alert")).toBeVisible();
    await expect(this.page).toHaveURL(/\/login/);
  }
}
