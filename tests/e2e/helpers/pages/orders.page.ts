import { Locator, Page } from "@playwright/test";
import { NavBar } from "./nav.page";

export class OrdersPage {
  readonly nav: NavBar;

  constructor(private readonly page: Page) {
    this.nav = new NavBar(page);
  }

  get heading(): Locator {
    return this.page.getByTestId("orders-heading");
  }

  get emptyState(): Locator {
    return this.page.getByTestId("orders-empty");
  }

  get list(): Locator {
    return this.page.getByTestId("orders-list");
  }

  get orderCards(): Locator {
    return this.page.getByTestId("order-card");
  }

  async goto() {
    await this.page.goto("/orders");
  }
}
