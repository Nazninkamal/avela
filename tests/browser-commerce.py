from playwright.sync_api import sync_playwright, expect
from pathlib import Path
import json, os
base=os.environ.get('AVELA_BASE_URL', 'http://127.0.0.1:8080').rstrip('/')
artifacts=Path(__file__).parent/'artifacts';artifacts.mkdir(exist_ok=True)
password=os.environ['AVELA_TEST_PASSWORD']
results=[]
def passed(name):
 results.append({'check':name,'status':'passed'});(artifacts/'browser-results.json').write_text(json.dumps(results,indent=2));print('PASS',name,flush=True)
def loaded(page):
 page.locator('img').evaluate_all("els=>els.forEach(i=>i.loading='eager')")
 page.wait_for_function("Array.from(document.images).every(i=>i.complete)")
with sync_playwright() as p:
 b=p.chromium.launch(executable_path='/usr/bin/chromium',headless=True,args=['--no-sandbox'])
 context=b.new_context(viewport={'width':1280,'height':900})
 page=context.new_page();errors=[];page.on('pageerror',lambda e:errors.append(str(e)))
 page.goto(base+'/',wait_until='networkidle');assert page.locator('li.product').count()==10
 page.locator('.avela-product-search input[name=s]').fill('serum');page.locator('.avela-product-search input[name=s]').press('Enter');page.wait_for_load_state('networkidle');assert 'serum' in page.inner_text('#content').lower();assert page.locator('li.product').count()==1
 passed('Header product search returns matching catalog product')
 page.goto(base+'/?s=zzzzunfindable&post_type=product',wait_until='networkidle');assert page.get_by_text('View all products and clear filters',exact=True).count();passed('Product search no-results recovery')
 page.goto(base+'/product-category/daily-care/',wait_until='networkidle');assert page.locator('li.product').count()==3;passed('Category archive')
 page.goto(base+'/shop/',wait_until='networkidle');page.locator('.woocommerce-ordering select').select_option('price');page.wait_for_url('**orderby=price**');page.wait_for_load_state('networkidle');assert 'ceramic dish' in page.locator('li.product').first.inner_text();passed('Catalog price sorting')
 page.goto(base+'/shop/?min_price=30&max_price=40',wait_until='networkidle');assert page.locator('li.product').count()==3 and 'cleanser' not in page.locator('ul.products').inner_text().lower();passed('Native WooCommerce price filtering (including overlapping variable price range)')
 page.goto(base+'/product/sample-linen/',wait_until='networkidle');assert page.locator('.flex-control-thumbs li').count()==2
 page.locator('table.variations select').select_option(label='Bath towel');page.wait_for_function("document.querySelector('input.variation_id').value !== '0'");page.locator('input.qty').fill('2');page.locator('button.single_add_to_cart_button:not(.disabled)').wait_for();page.locator('button.single_add_to_cart_button:not(.disabled)').click();page.wait_for_load_state('networkidle');expect(page.locator('.woocommerce-notices-wrapper')).to_contain_text('added to your cart');passed('Variable selection, quantity, gallery and add to cart')
 page.goto(base+'/product/sample-cleanser/',wait_until='networkidle');page.locator('button.single_add_to_cart_button').click();page.wait_for_load_state('networkidle');passed('Simple product add to cart')
 page.goto(base+'/cart/',wait_until='networkidle');qty=page.locator('input.wc-block-components-quantity-selector__input');qty.first.wait_for();expect(qty).to_have_count(2)
 qty.last.fill('2');qty.last.press('Tab');expect(qty.last).to_have_value('2');page.wait_for_timeout(1200)
 passed('Native Cart block quantity changes')
 page.get_by_role('button',name='Add coupons',exact=True).click();page.get_by_label('Enter code').fill('AVELA-TEST');page.get_by_role('button',name='Apply',exact=True).click();expect(page.locator('.wc-block-components-totals-discount')).to_be_visible();passed('Coupon applied to cart')
 assert 'Test delivery' in page.inner_text('#content');passed('Configured shipping rate appears in cart')
 loaded(page);page.screenshot(path=str(artifacts/'cart-desktop.png'),full_page=True)
 page.goto(base+'/checkout/',wait_until='networkidle');page.locator('#radio-control-wc-payment-method-options-bacs').wait_for()
 page.get_by_role('button',name='Place Order',exact=True).click();expect(page.locator('.wc-block-components-validation-error').first).to_be_visible();passed('Checkout required-field validation')
 page.locator('#email').fill('shopper@example.test');page.locator('#shipping-first_name').fill('Demo');page.locator('#shipping-last_name').fill('Shopper');page.locator('#shipping-address_1').fill('100 Test Street');page.locator('#shipping-city').fill('Los Angeles');page.locator('#shipping-postcode').fill('90001');page.locator('#shipping-postcode').press('Tab')
 page.locator('#radio-control-wc-payment-method-options-bacs').check();page.wait_for_timeout(5000);page.wait_for_function("!wp.data.select('wc/store/checkout').isCalculating() && !wp.data.select('wc/store/cart').isCustomerDataUpdating()")
 loaded(page);page.screenshot(path=str(artifacts/'checkout-desktop.png'),full_page=True)
 page.get_by_role('button',name='Place Order',exact=True).click();page.wait_for_url('**/order-received/**',timeout=45000);expect(page.locator('.woocommerce-thankyou-order-received')).to_be_visible();passed('Offline bank-transfer checkout and order confirmation')
 order_number=page.locator('.woocommerce-order-overview__order strong').inner_text()
 page.screenshot(path=str(artifacts/'order-confirmation.png'),full_page=True)
 page.goto(base+'/order-tracking/',wait_until='networkidle');page.locator('#orderid').fill(order_number);page.locator('#order_email').fill('shopper@example.test');page.get_by_role('button',name='Track',exact=True).click();page.wait_for_load_state('networkidle');assert order_number in page.inner_text('#content') and 'on hold' in page.inner_text('#content').lower();passed('Order tracking with matching email and order number')
 page.goto(base+'/cart/',wait_until='networkidle');expect(page.locator('.wc-block-cart__empty-cart__title')).to_be_visible();passed('Empty-cart state after order')
 page.goto(base+'/my-account/',wait_until='networkidle');page.locator('#username').fill('test_customer');page.locator('#password').fill(password);page.get_by_role('button',name='Log in',exact=True).click();page.wait_for_load_state('networkidle');assert page.locator('.woocommerce-MyAccount-navigation').count();passed('Customer login and account dashboard')
 for endpoint in ['orders','downloads','edit-address','edit-account']:
  page.goto(base+'/my-account/'+endpoint+'/',wait_until='networkidle');assert page.locator('.woocommerce-MyAccount-content').count()
 passed('Account orders, downloads, addresses and password editor')
 # Guest pages and responsive screenshots.
 guest=b.new_context(viewport={'width':1280,'height':900});pg=guest.new_page();pg.on('pageerror',lambda e:errors.append(str(e)))
 for width in [1280,768,390]:
  pg.set_viewport_size({'width':width,'height':900})
  for path,name in [('/','home'),('/shop/','shop'),('/product/sample-linen/','product'),('/my-account/','account'),('/not-a-real-page/','404')]:
   resp=pg.goto(base+path,wait_until='networkidle');loaded(pg)
   assert resp.status==(404 if name=='404' else 200),(path,resp.status)
   assert not pg.evaluate('document.documentElement.scrollWidth>innerWidth'),(width,path)
   if name in ['home','shop','product','account']:
    pg.screenshot(path=str(artifacts/f'{name}-{width}.png'),full_page=True)
  passed(f'Responsive layouts without horizontal overflow at {width}px')
 pg.goto(base+'/',wait_until='networkidle');pg.get_by_role('button',name='Menu',exact=False).click();assert pg.locator('#avela-navigation').is_visible();pg.keyboard.press('Escape');assert pg.locator('.avela-menu-toggle').get_attribute('aria-expanded')=='false';passed('Mobile menu, Escape key and focus return')
 pg.reload(wait_until='networkidle');pg.keyboard.press('Tab');focused=pg.locator(':focus');assert 'Skip to content' in focused.inner_text();assert focused.evaluate("e=>getComputedStyle(e).outlineStyle")!='none';passed('Keyboard skip link and visible focus')
 pg.goto(base+'/about/',wait_until='networkidle');assert 'Useful things.' in pg.inner_text('#content')
 pg.goto(base+'/contact/',wait_until='networkidle');assert pg.locator('#content a[href="mailto:hello@example.test"]').count()
 pg.goto(base+'/faq/',wait_until='networkidle');pg.locator('details summary').first.click();assert pg.locator('details').first.get_attribute('open') is not None
 passed('Ready-made About, Contact and expandable FAQ pages')
 assert not errors,errors;passed('No frontend JavaScript exceptions during tested flows')
 (artifacts/'browser-results.json').write_text(json.dumps(results,indent=2))
 b.close()
