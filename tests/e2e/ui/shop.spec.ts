import { test } from "@helpers/fixtures";

test.describe("Shop UI", () => {
  test("displays book catalog", async ({ shopPage }) => {
    await shopPage.goto();
    await shopPage.expectBookCount(8);
  });

  test("search filters books", async ({ shopPage }) => {
    await shopPage.goto();
    await shopPage.search("Orwell");
    await shopPage.expectBookCount(1);
    await shopPage.expectBookVisible("1984");
  });

  test("guest sees login to buy", async ({ shopPage }) => {
    await shopPage.goto();
    await shopPage.expectGuestLoginToBuy();
  });

  test("logged in user can open book detail", async ({ loggedInPage, shopPage }) => {
    await shopPage.goto();
    await shopPage.openFirstBook();
  });
});
