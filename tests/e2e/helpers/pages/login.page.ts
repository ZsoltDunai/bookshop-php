import { Page, expect } from "@playwright/test";
import { DEMO_USER } from "../constants";
import { NavBar } from "./nav.page";

export class LoginPage {
  readonly nav: NavBar;

  constructor(private readonly page: Page) {
    this.nav = new NavBar(page);
  }

  async goto() {
    await this.page.goto("/login");
    await this.expectLoaded();
  }

  async expectLoaded() {
    await expect(this.page).toHaveURL(/\/login/);
    await expect(this.page.getByTestId("login-heading")).toBeVisible();
  }

  async openFromHome() {
    await this.page.goto("/");
    await this.nav.clickLogin();
    await this.expectLoaded();
  }

  async fillCredentials(email: string, password: string) {
    await this.page.getByTestId("login-email").fill(email);
    await this.page.getByTestId("login-password").fill(password);
  }

  async submit() {
    await this.page.getByTestId("login-submit").click();
  }

  async login(email = DEMO_USER.email, password = DEMO_USER.password) {
    await this.goto();
    await this.fillCredentials(email, password);
    await this.submit();
    await Promise.race([
      this.page.waitForURL("/", { timeout: 15_000 }),
      this.page.getByTestId("login-alert").waitFor({ state: "visible", timeout: 15_000 }),
    ]);
  }

  async expectLoginError() {
    await expect(this.page.getByTestId("login-alert")).toBeVisible();
    await expect(this.page).toHaveURL(/\/login/);
  }

  async expectLoggedInAs(email: string) {
    await expect(this.page).toHaveURL("/");
    await this.nav.expectUser(email);
  }
}
