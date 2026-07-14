import { test, expect } from "@playwright/test";
import { DEMO_USER } from "@helpers/constants";

test.describe("HTTP security", () => {
  test("cart redirects unauthenticated users", async ({ request }) => {
    const response = await request.get("/cart", { maxRedirects: 0 });
    expect(response.status()).toBe(302);
    expect(response.headers().location).toContain("/login");
  });

  test("orders redirects unauthenticated users", async ({ request }) => {
    const response = await request.get("/orders", { maxRedirects: 0 });
    expect(response.status()).toBe(302);
    expect(response.headers().location).toContain("/login");
  });

  test("search escapes html payload", async ({ request }) => {
    const response = await request.get("/?q=%3Cscript%3Ealert(1)%3C%2Fscript%3E");
    expect(response.ok()).toBeTruthy();

    const html = await response.text();
    expect(html).not.toContain("<script>alert(1)</script>");
    expect(html).toContain("&lt;script&gt;alert(1)&lt;/script&gt;");
  });

  test("users have isolated carts", async ({ playwright }) => {
    const baseURL = process.env.BASE_URL || "http://127.0.0.1:8080";
    const userA = await playwright.request.newContext({ baseURL });
    const userB = await playwright.request.newContext({ baseURL });

    await userA.post("/login", {
      form: { email: DEMO_USER.email, password: DEMO_USER.password },
    });
    await userA.post("/cart/add", {
      form: { book_id: "1", quantity: "1", redirect: "/cart" },
    });

    const cartA = await userA.get("/cart");
    expect(await cartA.text()).toContain("The Great Gatsby");

    const email = `isolated-${Date.now()}@bookshop.io`;
    await userB.post("/register", {
      form: { email, password: "password123" },
    });

    const cartB = await userB.get("/cart");
    expect(await cartB.text()).toContain("Your cart is empty");

    await userA.dispose();
    await userB.dispose();
  });
});
