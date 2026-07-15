import { Locator, Page } from "@playwright/test";
import { DEMO_USER } from "../constants";
import { NavBar } from "./nav.page";

export class LoginPage {
  readonly nav: NavBar;

  constructor(private readonly page: Page) {
    this.nav = new NavBar(page);
  }

  get heading(): Locator {
    return this.page.getByTestId("login-heading");
  }

  get emailInput(): Locator {
    return this.page.getByTestId("login-email");
  }

  get passwordInput(): Locator {
    return this.page.getByTestId("login-password");
  }

  get submitButton(): Locator {
    return this.page.getByTestId("login-submit");
  }

  get alert(): Locator {
    return this.page.getByTestId("login-alert");
  }

  async goto() {
    await this.page.goto("/login");
  }

  async openFromHome() {
    await this.page.goto("/");
    await this.nav.clickLogin();
  }

  async fillCredentials(email: string, password: string) {
    await this.emailInput.fill(email);
    await this.passwordInput.fill(password);
  }

  async submit() {
    await this.submitButton.click();
  }

  /** Performs login and waits until navigation or error alert appears. */
  async login(email = DEMO_USER.email, password = DEMO_USER.password) {
    await this.goto();
    await this.fillCredentials(email, password);
    await this.submit();
    await Promise.race([
      this.page.waitForURL("/", { timeout: 15_000 }),
      this.alert.waitFor({ state: "visible", timeout: 15_000 }),
    ]);
  }
}
