import { Locator, Page } from "@playwright/test";
import { NavBar } from "./nav.page";

export class CartPage {
  readonly nav: NavBar;

  constructor(private readonly page: Page) {
    this.nav = new NavBar(page);
  }

  get heading(): Locator {
    return this.page.getByTestId("cart-heading");
  }

  get layout(): Locator {
    return this.page.getByTestId("cart-layout");
  }

  get items(): Locator {
    return this.page.getByTestId("cart-item");
  }

  get emptyState(): Locator {
    return this.page.getByTestId("cart-empty");
  }

  get checkoutButton(): Locator {
    return this.page.getByTestId("checkout-btn");
  }

  async goto() {
    await this.page.goto("/cart");
  }

  async checkout() {
    await this.checkoutButton.click();
  }
}
