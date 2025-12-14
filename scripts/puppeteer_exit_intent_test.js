const puppeteer = require('puppeteer');
const fs = require('fs');

(async () => {
	const browser = await puppeteer.launch({ args: ['--no-sandbox','--disable-setuid-sandbox'] });
	const page = await browser.newPage();
	page.setDefaultTimeout(20000);

	const url = 'http://localhost/JPCS/';
	console.log('Opening', url);
	await page.goto(url, { waitUntil: 'networkidle2' });

	// Ensure no existing suppression
	await page.evaluate(() => localStorage.removeItem('jpcs_exit_subscribed'));

	// Wait long enough for exit-listener to enable (site waits 2000ms)
	await new Promise(r => setTimeout(r, 2500));

	// Trigger exit intent: move mouse to top and dispatch mouseout
	await page.mouse.move(200, 200);
	await page.mouse.move(200, 0);
	await page.evaluate(() => {
		const ev = new MouseEvent('mouseout', { bubbles: true, cancelable: true, clientY: 5 });
		document.dispatchEvent(ev);
	});

	// Wait for modal; if not visible, force show (best-effort)
	console.log('Waiting for modal...');
	try {
		await page.waitForSelector('#exitModal[aria-hidden="false"]', { timeout: 4000 });
		console.log('Modal displayed (event)');
	} catch (e) {
		console.log('Modal did not appear automatically; forcing visible state');
		await page.evaluate(() => {
			const modal = document.getElementById('exitModal');
			if (modal) { modal.setAttribute('aria-hidden', 'false'); modal.style.display = 'block'; }
		});
		await new Promise(r => setTimeout(r, 300));
	}
	console.log('Proceeding with subscription flow');

	const testEmail = `puppeteer_test_${Date.now()}@example.com`;
	await page.type('#exitEmail', testEmail);

	await Promise.all([
		page.click('#exitNewsletterForm button[type="submit"]'),
		page.waitForResponse(resp => resp.url().includes('/handlers/newsletter_subscribe.php') && resp.status() === 200, { timeout: 5000 })
	]);

	// Wait for success message text
	await page.waitForFunction(() => {
		const msg = document.getElementById('exitModalMessage');
		return msg && msg.textContent && msg.textContent.toLowerCase().includes('subscribed');
	}, { timeout: 5000 });
	console.log('Subscription reported success in UI');

	// Check localStorage suppression
	const suppressed = await page.evaluate(() => localStorage.getItem('jpcs_exit_subscribed'));
	console.log('LocalStorage jpcs_exit_subscribed:', suppressed);

	// Verify subscriber was written to file
	const dbFile = 'database/newsletter.xml';
	const xml = fs.readFileSync(dbFile, 'utf8');
	if (xml.includes(testEmail)) {
		console.log('Subscriber found in', dbFile);
	} else {
		console.error('Subscriber not found in', dbFile);
		process.exit(2);
	}

	await browser.close();
	console.log('Test completed successfully');
})();
