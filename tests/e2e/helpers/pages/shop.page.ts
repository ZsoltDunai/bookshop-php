import { Page, expect } from "@playwright/test";
import { NavBar } from "./nav.page";

export class ShopPage {
  readonly nav: NavBar;

  constructor(private readonly page: Page) {
    this.nav = new NavBar(page);
  }

  async goto() {
    await this.page.goto("/");
    await this.expectLoaded();
  }

  async expectLoaded() {
    await expect(this.page.getByTestId("home-heading")).toBeVisible();
    await expect(this.page.getByTestId("book-grid")).toBeVisible();
  }

  async expectBookCount(count: number) {
    await expect(this.page.getByTestId("book-card")).toHaveCount(count);
  }

  async expectBookVisible(title: string) {
    await expect(this.page.getByText(title)).toBeVisible();
  }

  async expectGuestLoginToBuy() {
    await expect(this.page.getByTestId("login-to-buy").first()).toBeVisible();
  }

  async search(query: string) {
    await this.page.getByTestId("search-input").fill(query);
    await this.page.getByTestId("search-submit").click();
  }

  async addFirstBookToCart() {
    await this.page.getByTestId("add-to-cart").first().click();
    await expect(this.page.getByTestId("nav-cart-count")).toBeVisible();
  }

  async openFirstBook() {
    await this.page.getByTestId("book-card").first().getByRole("link").first().click();
    await this.expectBookDetail();
  }

  async expectBookDetail() {
    await expect(this.page.getByTestId("book-detail")).toBeVisible();
    await expect(this.page.getByTestId("book-title")).toBeVisible();
  }

  async expectCartBadge(count: number | string) {
    await this.nav.expectCartCount(count);
  }
}
