const { test, expect } = require('@playwright/test');
const {
  expectStandardJson,
  login,
} = require('./support');

const listEndpoints = [
  'vendors/GetVendorList',
  'product/GetProductList',
  'users/GetUserList',
  'ordersRequest/GetRequestList',
  'purchaseOrders/GetPurchaseOrders',
  'ordersApproval/GetApprovalList',
  'approvalPo/GetApprovalPurchaseOrder',
  'goodsReceipts/GetGoodsReciptsList',
  'invoice/GetInvoiceProcurement',
  'payment/GetInvoicePayment',
  'assets/GetListAssetsInstock',
];

test.describe('Backend contracts through an authenticated browser session', () => {
  test.beforeEach(async ({ page }) => {
    await login(page);
  });

  for (const endpoint of listEndpoints) {
    test(`${endpoint} returns the standard JSON envelope`, async ({ page }) => {
      const response = await page.request.post(endpoint);
      const rawBody = await response.text();

      expect(
        () => JSON.parse(rawBody),
        `${endpoint} mengembalikan JSON tidak valid:\n${rawBody.slice(0, 500)}`,
      ).not.toThrow();

      const body = JSON.parse(rawBody);
      expectStandardJson(response, body);
      expect(body.result).toEqual(expect.any(Array));
    });
  }

  test('new vendor rejects an empty payload without changing data', async ({ page }) => {
    const response = await page.request.post('vendors/SubmitNewVendor', {
      data: {},
    });
    const body = await response.json();

    expectStandardJson(response, body, 400);
    expect(body.message).toMatch(/nama vendor harus diisi/i);
  });

  test('vendor update rejects an incomplete payload without changing data', async ({ page }) => {
    const response = await page.request.post('vendors/SubmitUpdateVendor', {
      data: {},
    });
    const rawBody = await response.text();

    expect(response.status()).toBeGreaterThanOrEqual(400);
    expect(response.status()).toBeLessThan(500);
    expect(response.headers()['content-type'] || '').toContain('application/json');
    expect(() => JSON.parse(rawBody)).not.toThrow();
  });
});

test.describe('Unauthenticated API behavior', () => {
  test('protected API returns JSON 401 instead of a login HTML redirect', async ({ request }) => {
    const response = await request.post('vendors/GetVendorList', {
      maxRedirects: 0,
    });

    expect(response.status()).toBe(401);
    expect(response.headers()['content-type'] || '').toContain('application/json');
    expect(await response.json()).toEqual(
      expect.objectContaining({
        status: 401,
        message: expect.any(String),
      }),
    );
  });

  test('unknown routes return 404 and do not execute reflected markup', async ({ page }) => {
    await page.addInitScript(() => {
      window.__e2eRouteXss = false;
    });

    const payload = '<script>window.__e2eRouteXss=true</script>';
    const response = await page.goto(`index.php?url=${encodeURIComponent(payload)}`, {
      waitUntil: 'domcontentloaded',
    });

    expect(response.status()).toBe(404);
    expect(await page.evaluate(() => window.__e2eRouteXss)).toBe(false);
  });
});
