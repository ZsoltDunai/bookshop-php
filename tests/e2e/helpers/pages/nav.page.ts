import { Page, expect } from "@playwright/test";

/** Shared header/nav interactions used across pages. */
export class NavBar {
  constructor(private readonly page: Page) {}

  async clickLogin() {
    await this.page.getByTestId("nav-login").click();
  }

  async clickLogout() {
    await this.page.getByTestId("nav-logout").click();
  }

  async clickCart() {
    await this.page.getByTestId("nav-cart").click();
  }

  async expectUser(email: string) {
    await expect(this.page.getByTestId("nav-user")).toContainText(email);
  }

  async expectCartCount(count: number | string) {
    await expect(this.page.getByTestId("nav-cart-count")).toHaveText(String(count));
  }
}
