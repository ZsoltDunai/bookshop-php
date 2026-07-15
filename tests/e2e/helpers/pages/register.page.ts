import { Locator, Page } from "@playwright/test";
import { NavBar } from "./nav.page";

export class RegisterPage {
  readonly nav: NavBar;

  constructor(private readonly page: Page) {
    this.nav = new NavBar(page);
  }

  get heading(): Locator {
    return this.page.getByTestId("register-heading");
  }

  get emailInput(): Locator {
    return this.page.getByTestId("register-email");
  }

  get passwordInput(): Locator {
    return this.page.getByTestId("register-password");
  }

  get submitButton(): Locator {
    return this.page.getByTestId("register-submit");
  }

  get alert(): Locator {
    return this.page.getByTestId("register-alert");
  }

  async goto() {
    await this.page.goto("/register");
  }

  async fillForm(email: string, password: string) {
    await this.emailInput.fill(email);
    await this.passwordInput.fill(password);
  }

  async submit() {
    await this.submitButton.click();
  }

  /** Registers and waits until home navigation or error alert appears. */
  async register(email: string, password = "password123") {
    await this.goto();
    await this.fillForm(email, password);
    await this.submit();
    await Promise.race([
      this.page.waitForURL("/", { timeout: 15_000 }),
      this.alert.waitFor({ state: "visible", timeout: 15_000 }),
    ]);
  }

  async logout() {
    await this.nav.clickLogout();
  }
}
