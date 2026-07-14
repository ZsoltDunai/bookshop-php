import { test, expect } from "@helpers/fixtures";

test.describe("Shop UI", () => {
  test("displays book catalog", async ({ shopPage, page }) => {
    await shopPage.goto();
    await shopPage.expectLoaded();
    await expect(page.getByTestId("book-card")).toHaveCount(8);
  });

  test("search filters books", async ({ shopPage, page }) => {
    await shopPage.goto();
    await shopPage.search("Orwell");
    await expect(page.getByTestId("book-card")).toHaveCount(1);
    await expect(page.getByText("1984")).toBeVisible();
  });

  test("guest sees login to buy", async ({ page }) => {
    await page.goto("/");
    await expect(page.getByTestId("login-to-buy").first()).toBeVisible();
  });

  test("logged in user can open book detail", async ({ loggedInPage, shopPage, page }) => {
    await shopPage.goto();
    await shopPage.openFirstBook();
    await expect(page.getByTestId("book-detail")).toBeVisible();
    await expect(page.getByTestId("book-title")).toBeVisible();
  });
});
