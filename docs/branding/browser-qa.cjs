const fs=require('fs');
const {chromium}=require(process.env.PLAYWRIGHT_MODULE || 'playwright');
(async()=>{
 const browser=await chromium.launch({headless:true});
 const page=await browser.newPage(); const results=[];
 fs.mkdirSync('docs/branding/evidence',{recursive:true});
 for(const width of [320,360,768,1440]) for(const route of ['/en','/fr','/en/products','/fr/products','/en/contact','/fr/contact']) {
  await page.setViewportSize({width,height:900});
  const response=await page.goto('http://127.0.0.1:8765'+route,{waitUntil:'networkidle'});
  const data=await page.evaluate(()=>({title:document.title,overflow:document.documentElement.scrollWidth>innerWidth,unlabelled:[...document.querySelectorAll('input:not([type=hidden]),select,textarea')].filter(el=>!el.labels?.length&&!el.getAttribute('aria-label')).map(el=>el.name),h1:document.querySelectorAll('h1').length}));
  results.push({route,width,status:response.status(),...data});
  if([320,1440].includes(width)) await page.screenshot({path:'docs/branding/evidence/'+route.slice(1).replaceAll('/','-')+'-'+width+'.png',fullPage:true});
 }
 await page.setViewportSize({width:1440,height:900});await page.goto('http://127.0.0.1:8765/en');
 await page.keyboard.press('Tab');const skip=await page.locator(':focus').textContent();
 await page.keyboard.press('Enter');const mainFocused=await page.locator('#main-content').evaluate(el=>el===document.activeElement);
 await page.setViewportSize({width:320,height:900});await page.locator('.nav-toggle').click();const mobileExpanded=await page.locator('.nav-toggle').getAttribute('aria-expanded');
 fs.writeFileSync('docs/branding/evidence/browser-report.json',JSON.stringify({results,keyboard:{skip,mainFocused},mobileExpanded},null,2));
 console.log(JSON.stringify({pages:results.length,problems:results.filter(r=>r.status!==200||r.overflow||r.unlabelled.length),keyboard:{skip,mainFocused},mobileExpanded}));await browser.close();
})().catch(e=>{console.error(e);process.exit(1)});
