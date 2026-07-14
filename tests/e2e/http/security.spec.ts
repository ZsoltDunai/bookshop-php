import { test, expect } from "@playwright/test";
import { DEMO_USER } from "@helpers/constants";

test.describe("HTTP security", () => {
  test("cart API rejects unauthenticated users", async ({ request }) => {
    const response = await request.get("/api/cart");
    expect(response.status()).toBe(401);

    const body = await response.json();
    expect(body.detail).toBe("Could not validate credentials");
  });

  test("orders API rejects unauthenticated users", async ({ request }) => {
    const response = await request.get("/api/orders");
    expect(response.status()).toBe(401);
  });

  test("search API handles html payload safely", async ({ request }) => {
    const response = await request.get("/api/books?q=%3Cscript%3Ealert(1)%3C%2Fscript%3E");
    expect(response.ok()).toBeTruthy();

    const books = await response.json();
    expect(Array.isArray(books)).toBeTruthy();
    expect(books).toHaveLength(0);
  });

  test("users have isolated carts", async ({ playwright }) => {
    const baseURL = process.env.BASE_URL || "http://127.0.0.1:8080";
    const userA = await playwright.request.newContext({ baseURL });
    const userB = await playwright.request.newContext({ baseURL });

    const loginA = await userA.post("/api/auth/login", {
      data: { email: DEMO_USER.email, password: DEMO_USER.password },
    });
    const tokenA = (await loginA.json()).access_token;

    await userA.post("/api/cart/items", {
      headers: { Authorization: `Bearer ${tokenA}` },
      data: { book_id: 1, quantity: 1 },
    });

    const cartA = await userA.get("/api/cart", {
      headers: { Authorization: `Bearer ${tokenA}` },
    });
    expect(await cartA.text()).toContain("The Great Gatsby");

    const email = `isolated-${Date.now()}@bookshop.io`;
    await userB.post("/api/auth/register", {
      data: { email, password: "password123" },
    });
    const loginB = await userB.post("/api/auth/login", {
      data: { email, password: "password123" },
    });
    const tokenB = (await loginB.json()).access_token;

    const cartB = await userB.get("/api/cart", {
      headers: { Authorization: `Bearer ${tokenB}` },
    });
    const payload = await cartB.json();
    expect(payload.items).toHaveLength(0);

    await userA.dispose();
    await userB.dispose();
  });
});
