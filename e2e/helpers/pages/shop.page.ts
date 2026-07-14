import { Page, expect } from "@playwright/test";

export class ShopPage {
  constructor(private readonly page: Page) {}

  async goto() {
    await this.page.goto("/");
  }

  async expectLoaded() {
    await expect(this.page.getByTestId("home-heading")).toBeVisible();
    await expect(this.page.getByTestId("book-grid")).toBeVisible();
  }

  async search(query: string) {
    await this.page.getByTestId("search-input").fill(query);
    await this.page.getByTestId("search-submit").click();
  }

  async addFirstBookToCart() {
    await this.page.getByTestId("add-to-cart").first().click();
  }

  async openFirstBook() {
    await this.page.getByTestId("book-card").first().getByRole("link").first().click();
  }
}
