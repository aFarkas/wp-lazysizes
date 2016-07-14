
var chrome_ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';
var iphone_ua = 'Mozilla/5.0 (iPhone; CPU iPhone OS 6_1_4 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10B350 Safari/8536.25'


beforeEach(function () {
	window.lazySizesConfig = window.lazySizesConfig || {};
	window.lazySizesConfig.preloadAfterLoad = 'false';
	window.lazySizesConfig.expand = '359';
});


describe('checkIfMobile', function () {
	it('should exist', function () {
		expect(typeof window.lazySizesConfig.checkIfMobile === 'function').toBe(true);
	});

	it('should return false when given desktop Chrome useragent', function () {
		expect(window.lazySizesConfig.checkIfMobile(chrome_ua)).toBe(false);
	});

	it('should return true when given a phone useragent', function () {
		expect(window.lazySizesConfig.checkIfMobile(iphone_ua)).toBe(true);
	});
});


describe('setSmartPreload', function () {
	it('should exist', function () {
		expect(typeof window.lazySizesConfig.setSmartPreload === 'function').toBe(true);
	});

	it('should change the value of window.lazySizesConfig.preloadAfterLoad to boolean "true" or "false" if set to "smart"', function () {
		window.lazySizesConfig.preloadAfterLoad = 'smart';
		// pass false to simulate desktop
		window.lazySizesConfig.setSmartPreload( false );
		expect(window.lazySizesConfig.preloadAfterLoad).toBe(true);

		window.lazySizesConfig.preloadAfterLoad = 'smart';
		// pass true to simulate mobile
		window.lazySizesConfig.setSmartPreload( true )
		expect(window.lazySizesConfig.preloadAfterLoad).toBe(false);
	});

	it('should convert window.lazySizesConfig.preloadAfterLoad to boolean if not "smart", regardless of if mobile', function() {
		// simulate desktop with default 'false' preloadAfterLoad setting
		window.lazySizesConfig.setSmartPreload( false );
		expect(window.lazySizesConfig.preloadAfterLoad).toBe(false);

		// simulate mobile with default 'false' preloadAfterLoad setting
		window.lazySizesConfig.setSmartPreload( true );
		expect(window.lazySizesConfig.preloadAfterLoad).toBe(false);

		window.lazySizesConfig.preloadAfterLoad = 'true';

		// simulate desktop with preloadAfterLoad set to 'true'
		window.lazySizesConfig.setSmartPreload( false );
		expect(window.lazySizesConfig.preloadAfterLoad).toBe(true);

		// simulate mobile with preloadAfterLoad set to 'true'
		window.lazySizesConfig.setSmartPreload( true );
		expect(window.lazySizesConfig.preloadAfterLoad).toBe(true);
	});
});

describe('convertToInt', function () {
	it('should convert the "expand" value to an integer', function() {
		window.lazySizesConfig.convertToInt(window.lazySizesConfig.expand);
		expect(window.lazySizesConfig.expand).toBe(359);
	});
});
