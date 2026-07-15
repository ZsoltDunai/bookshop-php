import { Page, expect } from "@playwright/test";
import { NavBar } from "./nav.page";

export class RegisterPage {
  readonly nav: NavBar;

  constructor(private readonly page: Page) {
    this.nav = new NavBar(page);
  }

  async goto() {
    await this.page.goto("/register");
    await this.expectLoaded();
  }

  async expectLoaded() {
    await expect(this.page).toHaveURL(/\/register/);
    await expect(this.page.getByTestId("register-heading")).toBeVisible();
  }

  async fillForm(email: string, password: string) {
    await this.page.getByTestId("register-email").fill(email);
    await this.page.getByTestId("register-password").fill(password);
  }

  async submit() {
    await this.page.getByTestId("register-submit").click();
  }

  async register(email: string, password = "password123") {
    await this.goto();
    await this.fillForm(email, password);
    await this.submit();
    await Promise.race([
      this.page.waitForURL("/", { timeout: 15_000 }),
      this.page.getByTestId("register-alert").waitFor({ state: "visible", timeout: 15_000 }),
    ]);
  }

  async expectRegisteredAs(email: string) {
    await expect(this.page).toHaveURL("/");
    await this.nav.expectUser(email);
  }

  async expectRegisterError() {
    await expect(this.page.getByTestId("register-alert")).toBeVisible();
    await expect(this.page).toHaveURL(/\/register/);
  }

  async logout() {
    await this.nav.clickLogout();
  }
}
