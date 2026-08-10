const { expect } = require('@playwright/test');

const credentials = {
  username: process.env.E2E_USERNAME || 'admin',
  password: process.env.E2E_PASSWORD || 'admin123',
};

const configuredBaseUrl = (
  process.env.E2E_BASE_URL || 'http://localhost/e-procurement/'
).replace(/\/*$/, '/');
const loginUrlPattern = /\/(?:auth\/)?login(?:[/?#]|$)/i;
const dashboardUrlPattern = /\/dashboard(?:[/?#]|$)/i;
const phpDiagnosticPattern =
  /(?:Fatal error|Parse error|Uncaught (?:Error|Exception)|Warning:\s|Notice:\s|Deprecated:\s)/i;

async function login(page) {
  await page.goto('auth/login', { waitUntil: 'domcontentloaded' });
  await page.locator('input[name="username"]').fill(credentials.username);
  await page.locator('input[name="password"]').fill(credentials.password);

  await Promise.all([
    page.waitForURL(dashboardUrlPattern),
    page.getByRole('button', { name: /sign in/i }).click(),
  ]);

  await expect(page.getByRole('heading', { name: 'Dashboard', exact: true })).toBeVisible();
}

async function expectLoginPage(page) {
  await expect(page).toHaveURL(loginUrlPattern);
  await expect(page.locator('#loginForm')).toBeVisible();
  await expect(page.locator('input[name="password"]')).toHaveValue('');
}

async function expectNoPhpDiagnostics(page) {
  const body = await page.locator('body').innerText();
  expect(body, 'Halaman menampilkan diagnostic PHP ke pengguna').not.toMatch(
    phpDiagnosticPattern,
  );
}

function collectRuntimeFailures(page) {
  const failures = [];
  const targetOrigin = new URL(configuredBaseUrl).origin;

  page.on('pageerror', (error) => {
    failures.push(`JavaScript: ${error.message}`);
  });

  page.on('response', (response) => {
    const url = new URL(response.url());
    if (url.origin === targetOrigin && response.status() >= 400) {
      failures.push(`HTTP ${response.status()}: ${response.url()}`);
    }
  });

  return failures;
}

function expectStandardJson(response, body, expectedStatus = 200) {
  expect(response.status()).toBe(expectedStatus);
  expect(response.headers()['content-type'] || '').toContain('application/json');
  expect(body).toEqual(
    expect.objectContaining({
      status: expectedStatus,
      message: expect.any(String),
      result: expect.anything(),
    }),
  );
}

module.exports = {
  credentials,
  dashboardUrlPattern,
  expectLoginPage,
  expectNoPhpDiagnostics,
  expectStandardJson,
  collectRuntimeFailures,
  login,
  loginUrlPattern,
  phpDiagnosticPattern,
};
