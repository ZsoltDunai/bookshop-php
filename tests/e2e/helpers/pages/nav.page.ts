import { Locator, Page } from "@playwright/test";

/** Shared header/nav interactions — no assertions. */
export class NavBar {
  constructor(private readonly page: Page) {}

  get loginLink(): Locator {
    return this.page.getByTestId("nav-login");
  }

  get logoutButton(): Locator {
    return this.page.getByTestId("nav-logout");
  }

  get cartLink(): Locator {
    return this.page.getByTestId("nav-cart");
  }

  get userLabel(): Locator {
    return this.page.getByTestId("nav-user");
  }

  get cartCountBadge(): Locator {
    return this.page.getByTestId("nav-cart-count");
  }

  async clickLogin() {
    await this.loginLink.click();
  }

  async clickLogout() {
    await this.logoutButton.click();
  }

  async clickCart() {
    await this.cartLink.click();
  }
}
