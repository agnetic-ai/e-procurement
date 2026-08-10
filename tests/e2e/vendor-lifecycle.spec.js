const { test, expect } = require('@playwright/test');
const { expectStandardJson, login } = require('./support');

test.describe('Vendor lifecycle', () => {
  test.skip(
    process.env.E2E_ALLOW_MUTATION !== 'true',
    'Set E2E_ALLOW_MUTATION=true only for a disposable database.',
  );

  test('admin creates, finds, and updates a vendor', async ({ page }) => {
    await login(page);
    const formResponse = await page.request.get('vendor/RegisterVendor');
    const formHtml = await formResponse.text();
    const firstOption = (fieldName) => {
      const select = formHtml.match(
        new RegExp(`<select[^>]*name=["']${fieldName}["'][^>]*>([\\s\\S]*?)<\\/select>`, 'i'),
      );
      const option = select?.[1].match(/<option[^>]*value=["']([1-9]\d*)["']/i);
      return Number(option?.[1]);
    };

    const references = {
      cityId: firstOption('city_id'),
      businessType: firstOption('business_type_id'),
      paymentTerms: firstOption('payment_terms'),
    };

    expect(references.cityId).toBeGreaterThan(0);
    expect(references.businessType).toBeGreaterThan(0);
    expect(references.paymentTerms).toBeGreaterThan(0);

    const unique = `${Date.now()}-${test.info().workerIndex}`;
    const vendor = {
      vendorName: `E2E Vendor ${unique}`,
      vendorEmail: `e2e-vendor-${unique}@example.test`,
      vendorPhone: '081234567890',
      cityId: references.cityId,
      businessType: references.businessType,
      taxNumber: `E2E-${unique}`,
      paymentTerms: references.paymentTerms,
      website: 'https://example.test',
      address: 'E2E isolated database',
    };

    const createResponse = await page.request.post('vendors/SubmitNewVendor', {
      data: vendor,
    });
    const createBody = await createResponse.json();
    expectStandardJson(createResponse, createBody, 201);
    expect(createBody.result.vendorCode).toMatch(/^VND-\d{4}-\d{3,}$/);

    const vendorCode = createBody.result.vendorCode;
    await page.goto('vendor', { waitUntil: 'domcontentloaded' });
    await page.locator('.dt-search input').fill(vendorCode);
    await expect(page.locator('#vendorTableBody')).toContainText(vendor.vendorName);

    const updatedName = `${vendor.vendorName} Updated`;
    const updateResponse = await page.request.post('vendors/SubmitUpdateVendor', {
      data: {
        ...vendor,
        vendorName: updatedName,
        vendorCode,
      },
    });
    const updateBody = await updateResponse.json();
    expectStandardJson(updateResponse, updateBody, 201);

    await page.goto(`vendors/UpdateVendor?vendorCode=${encodeURIComponent(vendorCode)}`, {
      waitUntil: 'domcontentloaded',
    });
    await expect(page.locator('input[name="name"]')).toHaveValue(updatedName);
    await expect(page.locator('input[name="email"]')).toHaveValue(vendor.vendorEmail);

    const duplicateResponse = await page.request.post('vendors/SubmitNewVendor', {
      data: { ...vendor, vendorName: `${vendor.vendorName} Duplicate` },
    });
    const duplicateBody = await duplicateResponse.json();
    expectStandardJson(duplicateResponse, duplicateBody, 400);
    expect(duplicateBody.message).toMatch(/email sudah terdaftar/i);
  });
});
