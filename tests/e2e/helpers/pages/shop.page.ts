import { Locator, Page } from "@playwright/test";
import { NavBar } from "./nav.page";

export class ShopPage {
  readonly nav: NavBar;

  constructor(private readonly page: Page) {
    this.nav = new NavBar(page);
  }

  get heading(): Locator {
    return this.page.getByTestId("home-heading");
  }

  get bookGrid(): Locator {
    return this.page.getByTestId("book-grid");
  }

  get bookCards(): Locator {
    return this.page.getByTestId("book-card");
  }

  get searchInput(): Locator {
    return this.page.getByTestId("search-input");
  }

  get searchSubmit(): Locator {
    return this.page.getByTestId("search-submit");
  }

  get loginToBuyLinks(): Locator {
    return this.page.getByTestId("login-to-buy");
  }

  get addToCartButtons(): Locator {
    return this.page.getByTestId("add-to-cart");
  }

  get bookDetail(): Locator {
    return this.page.getByTestId("book-detail");
  }

  get bookTitle(): Locator {
    return this.page.getByTestId("book-title");
  }

  bookByTitle(title: string): Locator {
    return this.page.getByText(title);
  }

  async goto() {
    await this.page.goto("/");
  }

  async search(query: string) {
    await this.searchInput.fill(query);
    await this.searchSubmit.click();
  }

  async addFirstBookToCart() {
    await this.addToCartButtons.first().click();
    await this.nav.cartCountBadge.waitFor({ state: "visible", timeout: 15_000 });
  }

  async openFirstBook() {
    await this.bookCards.first().getByRole("link").first().click();
  }
}
