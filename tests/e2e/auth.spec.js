const { test, expect } = require('@playwright/test');
const {
  expectLoginPage,
  expectNoPhpDiagnostics,
  login,
  loginUrlPattern,
} = require('./support');

test.describe('Authentication and route protection', () => {
  test('login page renders the required controls without exposing a password', async ({ page }) => {
    await page.goto('auth/login', { waitUntil: 'domcontentloaded' });

    await expect(page).toHaveTitle(/eProcurement/i);
    await expect(page.locator('input[name="username"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toHaveAttribute('type', 'password');
    await expect(page.locator('input[name="password"]')).toHaveValue('');
    await expect(page.getByRole('button', { name: /sign in/i })).toBeEnabled();
    await expectNoPhpDiagnostics(page);
  });

  test('invalid credentials are rejected with a generic error', async ({ page }) => {
    const unknownUser = `e2e-unknown-${Date.now()}`;

    await page.goto('auth/login', { waitUntil: 'domcontentloaded' });
    await page.locator('input[name="username"]').fill(unknownUser);
    await page.locator('input[name="password"]').fill('definitely-wrong-password');
    await page.getByRole('button', { name: /sign in/i }).click();

    await expect(page).toHaveURL(loginUrlPattern);
    await expect(page.locator('.error-message')).toContainText('Invalid username or password');
    await expect(page.locator('body')).not.toContainText('SQLSTATE');
    await expectNoPhpDiagnostics(page);
  });

  test('valid credentials open the dashboard', async ({ page }) => {
    await login(page);
    await expect(page.locator('a[href*="auth/logout"]')).toBeAttached();
    await expectNoPhpDiagnostics(page);
  });

  test('logout invalidates the session and keeps protected pages inaccessible', async ({ page }) => {
    await login(page);
    await page.goto('auth/logout', { waitUntil: 'domcontentloaded' });

    await expectLoginPage(page);
    await expect(page.locator('.success-message')).toContainText('logged out', { ignoreCase: true });

    await page.goto('dashboard', { waitUntil: 'domcontentloaded' });
    await expectLoginPage(page);
  });

  for (const route of [
    'dashboard',
    'vendor',
    'product',
    'purchase',
    'ordersrequest',
    'purchaseOrders',
    'ordersapproval',
    'approvalPo',
    'goodsReceipts',
    'invoice',
    'invoiceVerification',
    'payment',
    'assets',
    'users',
  ]) {
    test(`anonymous user cannot access ${route}`, async ({ page }) => {
      await page.goto(route, { waitUntil: 'domcontentloaded' });
      await expectLoginPage(page);
    });
  }

  test('email approval is the only anonymous orders-approval route', async ({ page }) => {
    await page.goto('ordersapproval/GetApprovalList?token=invalid', {
      waitUntil: 'domcontentloaded',
    });
    await expectLoginPage(page);

    await page.goto('ordersapproval/ApproveByEmail?token=invalid&action=approve', {
      waitUntil: 'domcontentloaded',
    });
    await expect(page).not.toHaveURL(loginUrlPattern);
    await expect(page.locator('body')).toContainText(/token tidak ditemukan|link tidak valid/i);
    await expectNoPhpDiagnostics(page);
  });
});
