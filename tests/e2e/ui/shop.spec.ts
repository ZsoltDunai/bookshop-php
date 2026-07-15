import { test, expect } from "@helpers/fixtures";

test.describe("Shop UI", () => {
  test("displays book catalog", async ({ shopPage }) => {
    await shopPage.goto();
    await expect(shopPage.heading).toBeVisible();
    await expect(shopPage.bookGrid).toBeVisible();
    await expect(shopPage.bookCards).toHaveCount(8);
  });

  test("search filters books", async ({ shopPage }) => {
    await shopPage.goto();
    await shopPage.search("Orwell");
    await expect(shopPage.bookCards).toHaveCount(1);
    await expect(shopPage.bookByTitle("1984")).toBeVisible();
  });

  test("guest sees login to buy", async ({ shopPage }) => {
    await shopPage.goto();
    await expect(shopPage.loginToBuyLinks.first()).toBeVisible();
  });

  test("logged in user can open book detail", async ({ loggedInPage, shopPage }) => {
    await shopPage.goto();
    await shopPage.openFirstBook();
    await expect(shopPage.bookDetail).toBeVisible();
    await expect(shopPage.bookTitle).toBeVisible();
  });
});
