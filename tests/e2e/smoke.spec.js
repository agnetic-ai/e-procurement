const { test, expect } = require('@playwright/test');
const {
  collectRuntimeFailures,
  expectNoPhpDiagnostics,
  login,
  loginUrlPattern,
} = require('./support');

const pages = [
  ['dashboard', 'Dashboard'],
  ['vendor', 'Vendor Management'],
  ['vendor/RegisterVendor', 'Add New Vendor'],
  ['product', 'Product Management'],
  ['product/NewProduct', 'Add New Product'],
  ['purchase', 'Purchase Request'],
  ['ordersrequest', 'Order Request Management'],
  ['purchaseOrders', 'Purchase Orders'],
  ['ordersapproval', 'Order Approval Management'],
  ['approvalPo', 'Approval Purchase Order'],
  ['goodsReceipts', 'Goods Receipts'],
  ['invoice', 'Invoice Management'],
  ['invoice/CreateInvoice', 'Create Invoice Management'],
  ['invoiceVerification', 'Invoice Verification'],
  ['payment', 'Invoices Ready for Payment'],
  ['assets', 'Asset Management'],
  ['assets/stock', 'Asset Stock'],
  ['users', 'User Management'],
];

test.describe('Authenticated application smoke test', () => {
  test.beforeEach(async ({ page }) => {
    await login(page);
  });

  for (const [route, heading] of pages) {
    test(`${route} renders without application errors`, async ({ page }) => {
      const runtimeFailures = collectRuntimeFailures(page);
      const response = await page.goto(route, { waitUntil: 'domcontentloaded' });

      expect(response, `Tidak menerima response untuk ${route}`).not.toBeNull();
      expect(response.status(), `HTTP gagal pada ${route}`).toBeLessThan(400);
      await expect(page).not.toHaveURL(loginUrlPattern);
      await expect(page.getByRole('heading', { name: heading, exact: true }).first()).toBeVisible();
      await expectNoPhpDiagnostics(page);

      // Beri AJAX halaman kesempatan selesai agar error async ikut tertangkap.
      await page.waitForTimeout(750);
      expect(runtimeFailures, runtimeFailures.join('\n')).toEqual([]);
    });
  }

  test('vendor list renders backend data through DataTables', async ({ page }) => {
    const listResponse = page.waitForResponse(
      (response) =>
        response.url().includes('vendors/GetVendorList') &&
        response.request().method() === 'POST',
    );

    await page.goto('vendor', { waitUntil: 'domcontentloaded' });
    const response = await listResponse;
    expect(response.status()).toBe(200);

    const body = await response.json();
    expect(body.status).toBe(200);
    expect(body.result).toEqual(expect.any(Array));
    await expect(page.locator('#vendorsTable')).toBeVisible();

    if (body.result.length > 0) {
      const visibleRows = Math.min(body.result.length, 10);
      await expect(page.locator('#vendorTableBody tr')).toHaveCount(visibleRows);
      await page.locator('.dt-search input').fill(body.result[0].vendorCode);
      await expect(page.locator('#vendorTableBody tr')).toHaveCount(1);
      await expect(page.locator('#vendorTableBody')).toContainText(body.result[0].vendorCode);
    }
  });

  test('vendor form enforces required browser validation', async ({ page }) => {
    await page.goto('vendor/RegisterVendor', { waitUntil: 'domcontentloaded' });
    await page.getByRole('button', { name: 'Submit', exact: true }).click();

    const firstInvalidField = page.locator('#vendorForm :invalid').first();
    await expect(firstInvalidField).toHaveAttribute('name', 'name');
    await expect(page).toHaveURL(/vendor\/RegisterVendor/i);
  });
});
