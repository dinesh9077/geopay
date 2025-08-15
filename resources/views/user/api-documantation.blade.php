<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Geopay Service – Merchant API Docs</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
  :root { --bg:#ffffff; --text:#222; --muted:#555; --head:#102030; --accent:#81a8c7; --border:#e5e8ec; --code:#0b1220; }
  *{box-sizing:border-box}
  body{margin:0;background:var(--bg);color:var(--text);font:16px/1.6 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Inter,Arial,sans-serif}
  header{background:var(--head);color:#fff;padding:24px 20px}
  header h1{margin:0;font-size:22px}
  .container{display:grid;grid-template-columns:300px 1fr;gap:0;min-height:100vh}
  nav{border-right:1px solid var(--border);padding:18px;position:sticky;top:0;max-height:100vh;overflow:auto;background:#fafcfe}
  nav h3{margin:14px 0 8px 0;color:var(--head);font-size:13px;letter-spacing:.06em;text-transform:uppercase}
  nav a{display:block;text-decoration:none;color:#1f2937;padding:8px;border-radius:8px;margin-bottom:4px}
  nav a:hover{background:#f0f6fa}
  .pill{display:inline-block;background:#eef6fb;border:1px solid var(--accent);color:#1f2937;border-radius:999px;padding:2px 8px;font-size:12px;margin-left:8px}
  main{padding:24px}
  h2{color:var(--head);border-bottom:3px solid var(--accent);padding-bottom:6px}
  h3{color:var(--head)}
  .card{background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px;margin-bottom:16px}
  .endpoint{border:1px solid var(--border);border-radius:12px;margin:16px 0;overflow:hidden}
  .endpoint .head{display:flex;gap:12px;align-items:center;padding:12px 16px;background:#f4f8fb;border-bottom:1px solid var(--border);cursor:pointer}
  .method{font-weight:700;border-radius:8px;border:1px solid var(--accent);padding:2px 8px;color:var(--head)}
  .url{font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;flex:1}
  .endpoint .body{display:none;padding:16px}
  .endpoint.open .body{display:block}
  table{width:100%;border-collapse:collapse;margin:10px 0}
  th,td{border:1px solid var(--border);padding:8px;vertical-align:top}
  th{background:#f9fbfd;text-align:left}
  pre{background:#f7fafc;border:1px solid var(--border);border-radius:8px;padding:12px;overflow:auto}
  code{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace}
  .muted{color:var(--muted)}
  .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px}
  .search{margin-left:auto}
  .search input{width:320px;max-width:40vw;padding:10px 12px;border:1px solid var(--border);border-radius:10px}
  footer{text-align:center;color:#6b7280;padding:20px}
</style>
</head>
<body>
<header>
  <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
    <h1>Geopay Service – Merchant API Documentation</h1>
    <span class="pill">Generated 2025-08-15 10:43:30</span>
    <div class="search"><input id="search" placeholder="Search endpoints (name, path, method)…"></div>
  </div>
</header>
<div class="container">
  <nav>
    <h3>Sections</h3>
    <a href="#overview">Overview</a>
    <a href="#auth">Authentication</a>
    <a href="#errors">Response Format</a>
    <a href="#endpoints">Endpoints</a>
    <h3>Tags</h3>
<a href='#Auth'>#Auth <span class='pill'>2</span></a><a href='#Transfer Bank'>#Transfer Bank <span class='pill'>4</span></a><a href='#Transfer Mobile Money'>#Transfer Mobile Money <span class='pill'>3</span></a><a href='#Webhooks'>#Webhooks <span class='pill'>2</span></a><a href='#General'>#General <span class='pill'>2</span></a>
  </nav>
  <main>
    <section id="overview" class="card">
      <h2>Overview</h2>
      <p>Welcome to the Geopay Merchant API. All requests must be sent over HTTPS using JSON payloads and the <code>Content-Type: application/json</code> header.</p>
      <div class="grid">
        <div class="card"><b>Base URL</b><br><code>{{ $credential->api_url }}</code></div>
        <div class="card"><b>Default Headers</b><br><code>Content-Type: application/json</code></div>
      </div>
    </section>

    <section id="auth" class="card">
      <h2>Authentication</h2>
      <p>Include the token in the <code>Authorization</code> header as a Bearer token.</p>
      <table>
        <thead><tr><th>Header</th><th>Value</th><th>Notes</th></tr></thead>
        <tbody>
          <tr><td><code>Authorization</code></td><td><code>Bearer {bearer_token}</code></td><td>Include for protected endpoints.</td></tr>
          <tr><td><code>Content-Type</code></td><td><code>application/json</code></td><td>All requests use JSON.</td></tr>
        </tbody>
      </table>
    </section>

    <section id="errors" class="card">
      <h2>Response Format (ApiServiceResponseTrait)</h2>
      <div class="grid">
        <div class="card"><b>Success</b><pre><code>{
  &quot;status&quot;: true,
  &quot;message&quot;: &quot;Operation successful&quot;,
  &quot;data&quot;: {
    &quot;example&quot;: &quot;value&quot;
  }
}</code></pre></div>
        <div class="card"><b>Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;An error occurred&quot;,
  &quot;error_code&quot;: &quot;ERROR_CODE&quot;,
  &quot;data&quot;: null
}</code></pre></div>
        <div class="card"><b>Validation Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;Validation Failed&quot;,
  &quot;error_code&quot;: &quot;VALIDATION_ERROR&quot;,
  &quot;data&quot;: {
    &quot;field_name&quot;: [
      &quot;The field_name field is required.&quot;
    ]
  }
}</code></pre></div>
      </div>
    </section>

    <section id="endpoints" class="card">
      <h2>Endpoints</h2>
<h3 id='Auth'>#Auth</h3>
        <div class="endpoint" data-name="access token generate" data-url="{{ $credential->api_url }}/auth/token" data-method="POST">
          <div class="head" onclick="this.parentElement.classList.toggle('open')">
            <span class="method">POST</span>
            <div class="url">{{ $credential->api_url }}/auth/token</div>
            <span class="pill">Access Token Generate</span>
          </div>
          <div class="body">
            
            <h4>Headers</h4>
            <table><thead><tr><th>Header</th><th>Value</th><th>Notes</th></tr></thead><tbody><tr><td><code>Content-Type</code></td><td><code>application/json</code></td><td></td></tr></tbody></table>
            <h4>Parameters</h4><table><thead><tr><th>Name</th><th>Type</th><th>Example</th></tr></thead><tbody><tr><td><code>client_id</code></td><td>str</td><td><code>{your_client_id}</code></td></tr><tr><td><code>secret_id</code></td><td>str</td><td><code>{your_secret_id}</code></td></tr><tr><td><code>ttl</code></td><td>bool</td><td><code>True</code></td></tr></tbody></table>
            <h4>Request Body</h4><pre><code>{   
    &quot;client_id&quot;:&quot;{your_client_id}&quot;,
    &quot;secret_id&quot;:&quot;{your_secret_id}&quot;, 
    &quot;ttl&quot;: true // 30, 60, true = parmanent
}</code></pre>
            <h4>Example Responses</h4><b>Success</b><pre><code>{
  &quot;status&quot;: true,
  &quot;message&quot;: &quot;Access Token Generate executed successfully&quot;,
  &quot;data&quot;: {
    &quot;example&quot;: &quot;value&quot;
  }
}</code></pre><b>Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;An error occurred&quot;,
  &quot;error_code&quot;: &quot;ERROR_CODE&quot;,
  &quot;data&quot;: null
}</code></pre><b>Validation Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;Validation Failed&quot;,
  &quot;error_code&quot;: &quot;VALIDATION_ERROR&quot;,
  &quot;data&quot;: {
    &quot;field_name&quot;: [
      &quot;The field_name field is required.&quot;
    ]
  }
}</code></pre>
          </div>
        </div>
        
         
        <div class="endpoint" data-name="revoke token" data-url="{{ $credential->api_url }}/auth/token/revoke" data-method="POST">
          <div class="head" onclick="this.parentElement.classList.toggle('open')">
            <span class="method">POST</span>
            <div class="url">{{ $credential->api_url }}/auth/token/revoke</div>
            <span class="pill">Revoke Token</span>
          </div>
          <div class="body">
            <p class='muted'>Generated from cURL: curl -X POST http://localhost:8000/api/auth/token/revoke \
  -H &quot;Authorization: Bearer 1001.nL2k19F3sM3Tn6z...&quot;
</p>
            <h4>Headers</h4>
            <table><thead><tr><th>Header</th><th>Value</th><th>Notes</th></tr></thead><tbody><tr><td><code>Authorization</code></td><td><code>Bearer 1001.nL2k19F3sM3Tn6z...</code></td><td></td></tr><tr><td><code>Content-Type</code></td><td><code>application/json</code></td><td></td></tr></tbody></table>
            
            
            <h4>Example Responses</h4><b>Success</b><pre><code>{
  &quot;status&quot;: true,
  &quot;message&quot;: &quot;Revoke Token executed successfully&quot;,
  &quot;data&quot;: {
    &quot;example&quot;: &quot;value&quot;
  }
}</code></pre><b>Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;An error occurred&quot;,
  &quot;error_code&quot;: &quot;ERROR_CODE&quot;,
  &quot;data&quot;: null
}</code></pre><b>Validation Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;Validation Failed&quot;,
  &quot;error_code&quot;: &quot;VALIDATION_ERROR&quot;,
  &quot;data&quot;: {
    &quot;field_name&quot;: [
      &quot;The field_name field is required.&quot;
    ]
  }
}</code></pre>
          </div>
        </div>
        <h3 id='Transfer Bank'>#Transfer Bank</h3>
        <div class="endpoint" data-name="country list" data-url="{{ $credential->api_url }}/transfer-bank/country-list" data-method="GET">
          <div class="head" onclick="this.parentElement.classList.toggle('open')">
            <span class="method">GET</span>
            <div class="url">{{ $credential->api_url }}/transfer-bank/country-list</div>
            <span class="pill">Country List</span>
          </div>
          <div class="body">
            
            <h4>Headers</h4>
            <table><thead><tr><th>Header</th><th>Value</th><th>Notes</th></tr></thead><tbody><tr><td><code>Content-Type</code></td><td><code>application/json</code></td><td></td></tr></tbody></table>
            
            
            <h4>Example Responses</h4><b>Success</b><pre><code>{
  &quot;status&quot;: true,
  &quot;message&quot;: &quot;Country List executed successfully&quot;,
  &quot;data&quot;: {
    &quot;example&quot;: &quot;value&quot;
  }
}</code></pre><b>Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;An error occurred&quot;,
  &quot;error_code&quot;: &quot;ERROR_CODE&quot;,
  &quot;data&quot;: null
}</code></pre><b>Validation Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;Validation Failed&quot;,
  &quot;error_code&quot;: &quot;VALIDATION_ERROR&quot;,
  &quot;data&quot;: {
    &quot;field_name&quot;: [
      &quot;The field_name field is required.&quot;
    ]
  }
}</code></pre>
          </div>
        </div>
        
        <div class="endpoint" data-name="bank list" data-url="{{ $credential->api_url }}/transfer-bank/bank-list" data-method="POST">
          <div class="head" onclick="this.parentElement.classList.toggle('open')">
            <span class="method">POST</span>
            <div class="url">{{ $credential->api_url }}/transfer-bank/bank-list</div>
            <span class="pill">Bank List</span>
          </div>
          <div class="body">
            
            <h4>Headers</h4>
            <table><thead><tr><th>Header</th><th>Value</th><th>Notes</th></tr></thead><tbody><tr><td><code>Content-Type</code></td><td><code>application/json</code></td><td></td></tr></tbody></table>
            <h4>Parameters</h4><table><thead><tr><th>Name</th><th>Type</th><th>Example</th></tr></thead><tbody><tr><td><code>payoutCountry</code></td><td>str</td><td><code>BGD</code></td></tr><tr><td><code>service</code></td><td>int</td><td><code>1</code></td></tr><tr><td><code>payoutIso</code></td><td>str</td><td><code></code></td></tr></tbody></table>
            <h4>Request Body</h4><pre><code>{
    &quot;payoutCountry&quot;: &quot;BGD&quot;,
    &quot;service&quot;: 1,
    &quot;payoutIso&quot;: &quot;&quot; //optional for service 1
}</code></pre>
            <h4>Example Responses</h4><b>Success</b><pre><code>{
  &quot;status&quot;: true,
  &quot;message&quot;: &quot;Bank List executed successfully&quot;,
  &quot;data&quot;: {
    &quot;example&quot;: &quot;value&quot;
  }
}</code></pre><b>Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;An error occurred&quot;,
  &quot;error_code&quot;: &quot;ERROR_CODE&quot;,
  &quot;data&quot;: null
}</code></pre><b>Validation Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;Validation Failed&quot;,
  &quot;error_code&quot;: &quot;VALIDATION_ERROR&quot;,
  &quot;data&quot;: {
    &quot;field_name&quot;: [
      &quot;The field_name field is required.&quot;
    ]
  }
}</code></pre>
          </div>
        </div>
        
        <div class="endpoint" data-name="fields by bank" data-url="{{ $credential->api_url }}/transfer-bank/get-fields" data-method="POST">
          <div class="head" onclick="this.parentElement.classList.toggle('open')">
            <span class="method">POST</span>
            <div class="url">{{ $credential->api_url }}/transfer-bank/get-fields</div>
            <span class="pill">fields by bank</span>
          </div>
          <div class="body">
            
            <h4>Headers</h4>
            <table><thead><tr><th>Header</th><th>Value</th><th>Notes</th></tr></thead><tbody><tr><td><code>Content-Type</code></td><td><code>application/json</code></td><td></td></tr><tr><td><code>Accept</code></td><td><code>application/json</code></td><td></td></tr></tbody></table>
            <h4>Parameters</h4><table><thead><tr><th>Name</th><th>Type</th><th>Example</th></tr></thead><tbody><tr><td><code>payoutCountry</code></td><td>str</td><td><code>BGD</code></td></tr><tr><td><code>payoutCurrency</code></td><td>str</td><td><code>BDT</code></td></tr><tr><td><code>service</code></td><td>int</td><td><code>1</code></td></tr><tr><td><code>locationId</code></td><td>str</td><td><code>BGDAB 3237</code></td></tr></tbody></table>
            <h4>Request Body</h4><pre><code>{
    &quot;payoutCountry&quot;: &quot;BGD&quot;,
    &quot;payoutCurrency&quot;: &quot;BDT&quot;,
    &quot;service&quot;: 1,
    &quot;locationId&quot;: &quot;BGDAB 3237&quot;
}</code></pre>
            <h4>Example Responses</h4><b>Success</b><pre><code>{
  &quot;status&quot;: true,
  &quot;message&quot;: &quot;fields by bank executed successfully&quot;,
  &quot;data&quot;: {
    &quot;example&quot;: &quot;value&quot;
  }
}</code></pre><b>Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;An error occurred&quot;,
  &quot;error_code&quot;: &quot;ERROR_CODE&quot;,
  &quot;data&quot;: null
}</code></pre><b>Validation Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;Validation Failed&quot;,
  &quot;error_code&quot;: &quot;VALIDATION_ERROR&quot;,
  &quot;data&quot;: {
    &quot;field_name&quot;: [
      &quot;The field_name field is required.&quot;
    ]
  }
}</code></pre>
          </div>
        </div>
        
        <div class="endpoint" data-name="create transaction" data-url="{{ $credential->api_url }}/transfer-bank/create-trasaction" data-method="POST">
          <div class="head" onclick="this.parentElement.classList.toggle('open')">
            <span class="method">POST</span>
            <div class="url">{{ $credential->api_url }}/transfer-bank/create-trasaction</div>
            <span class="pill">Create Transaction</span>
          </div>
          <div class="body">
            
            <h4>Headers</h4>
            <table><thead><tr><th>Header</th><th>Value</th><th>Notes</th></tr></thead><tbody><tr><td><code>Content-Type</code></td><td><code>application/json</code></td><td></td></tr></tbody></table>
            <h4>Parameters</h4><table><thead><tr><th>Name</th><th>Type</th><th>Example</th></tr></thead><tbody><tr><td><code>service</code></td><td>int</td><td><code>1</code></td></tr><tr><td><code>amount</code></td><td>str</td><td><code>2.00</code></td></tr><tr><td><code>exchange_rate_id</code></td><td>int</td><td><code>1</code></td></tr><tr><td><code>exchange_rate</code></td><td>str</td><td><code>634.651875</code></td></tr><tr><td><code>transferamount</code></td><td>str</td><td><code>1269.30375</code></td></tr><tr><td><code>remitcurrency</code></td><td>str</td><td><code>USD</code></td></tr><tr><td><code>payoutCurrency</code></td><td>str</td><td><code>BDT</code></td></tr><tr><td><code>payoutIso</code></td><td>str</td><td><code>BG</code></td></tr><tr><td><code>bankId</code></td><td>str</td><td><code>BGDAB</code></td></tr><tr><td><code>remittertype</code></td><td>str</td><td><code>I</code></td></tr><tr><td><code>senderfirstname</code></td><td>str</td><td><code>Dinesh</code></td></tr><tr><td><code>sendermiddlename</code></td><td>str</td><td><code></code></td></tr><tr><td><code>senderlastname</code></td><td>str</td><td><code>Patil</code></td></tr><tr><td><code>sendergender</code></td><td>str</td><td><code>Male</code></td></tr><tr><td><code>senderaddress</code></td><td>str</td><td><code>Softieons Abhinandan royal Surat Gujarat 395007, India</code></td></tr><tr><td><code>sendercity</code></td><td>str</td><td><code>India</code></td></tr><tr><td><code>senderstate</code></td><td>str</td><td><code>Gujarat</code></td></tr><tr><td><code>senderzipcode</code></td><td>str</td><td><code>395854</code></td></tr><tr><td><code>fromCountry</code></td><td>str</td><td><code>IN</code></td></tr><tr><td><code>sendercountry</code></td><td>str</td><td><code>IND</code></td></tr><tr><td><code>sendermobile</code></td><td>str</td><td><code>917507642090</code></td></tr><tr><td><code>sendernationality</code></td><td>str</td><td><code>IND</code></td></tr><tr><td><code>senderidtype</code></td><td>str</td><td><code>01</code></td></tr><tr><td><code>senderidtyperemarks</code></td><td>str</td><td><code>Passport</code></td></tr><tr><td><code>senderidnumber</code></td><td>str</td><td><code>754678</code></td></tr><tr><td><code>senderidissuecountry</code></td><td>str</td><td><code>IND</code></td></tr><tr><td><code>senderidissuedate</code></td><td>str</td><td><code>2025-08-12</code></td></tr><tr><td><code>senderidexpiredate</code></td><td>str</td><td><code>2025-08-30</code></td></tr><tr><td><code>senderdateofbirth</code></td><td>str</td><td><code>1995-08-04</code></td></tr><tr><td><code>senderoccupation</code></td><td>str</td><td><code>07</code></td></tr><tr><td><code>senderoccupationremarks</code></td><td>str</td><td><code>Agriculture forestry fisheries</code></td></tr><tr><td><code>sendersourceoffund</code></td><td>str</td><td><code>01</code></td></tr><tr><td><code>sendersourceoffundremarks</code></td><td>str</td><td><code>Salary to include any work related compensation and pensions</code></td></tr><tr><td><code>senderemail</code></td><td>str</td><td><code>dinesh.softieons@gmail.com</code></td></tr><tr><td><code>sendernativefirstname</code></td><td>str</td><td><code></code></td></tr><tr><td><code>senderbeneficiaryrelationship</code></td><td>str</td><td><code></code></td></tr><tr><td><code>senderbeneficiaryrelationshipremarks</code></td><td>str</td><td><code></code></td></tr><tr><td><code>purposeofremittance</code></td><td>str</td><td><code>16</code></td></tr><tr><td><code>purposeofremittanceremark</code></td><td>str</td><td><code></code></td></tr><tr><td><code>beneficiarytype</code></td><td>str</td><td><code>I</code></td></tr><tr><td><code>receiverfirstname</code></td><td>str</td><td><code>Ebrahim</code></td></tr><tr><td><code>receivermiddlename</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receiverlastname</code></td><td>str</td><td><code>kadir</code></td></tr><tr><td><code>receiveraddress</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receivercontactnumber</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receiverstate</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receiverareatown</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receivercity</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receiverzipcode</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receivercountry</code></td><td>str</td><td><code>BGD</code></td></tr><tr><td><code>receiveridtype</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receiveridtyperemarks</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receiveroccupation</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receiveroccupationremark</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receiveridnumber</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receiveremail</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receivernativefirstname</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receivernativemiddlename</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receivernativelastname</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receivernativeaddress</code></td><td>str</td><td><code></code></td></tr><tr><td><code>sendersecondaryidtype</code></td><td>str</td><td><code>01</code></td></tr><tr><td><code>sendersecondaryidnumber</code></td><td>str</td><td><code>754678</code></td></tr><tr><td><code>sendernativelastname</code></td><td>str</td><td><code></code></td></tr><tr><td><code>calcby</code></td><td>str</td><td><code>P</code></td></tr><tr><td><code>paymentmode</code></td><td>str</td><td><code>B</code></td></tr><tr><td><code>bankName</code></td><td>str</td><td><code>AB BANK</code></td></tr><tr><td><code>bankbranchname</code></td><td>str</td><td><code></code></td></tr><tr><td><code>bankbranchcode</code></td><td>str</td><td><code>7778asdasd</code></td></tr><tr><td><code>bankaccountnumber</code></td><td>str</td><td><code>100801463700</code></td></tr><tr><td><code>swiftcode</code></td><td>str</td><td><code></code></td></tr><tr><td><code>promotioncode</code></td><td>str</td><td><code></code></td></tr><tr><td><code>sendernativeaddress</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receivernationality</code></td><td>str</td><td><code>BGD</code></td></tr><tr><td><code>receiveridissuedate</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receiveridexpiredate</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receiverdistrict</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receiptcpf</code></td><td>str</td><td><code></code></td></tr><tr><td><code>notes</code></td><td>str</td><td><code>test</code></td></tr><tr><td><code>receiverdateofbirth</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receivergender</code></td><td>str</td><td><code></code></td></tr><tr><td><code>receiveraccounttype</code></td><td>str</td><td><code></code></td></tr></tbody></table>
            <h4>Request Body</h4><pre><code>{
    &quot;service&quot;: 1, // allow only values 1 or 2, we get the country list based on this
    &quot;amount&quot;: &quot;2.00&quot;, // amount in USD
    &quot;exchange_rate_id&quot;: 1, // exchange rate ID
    &quot;exchange_rate&quot;: &quot;634.651875&quot;, // current exchange rate
    &quot;transferamount&quot;: &quot;1269.30375&quot;, // transfer amount (amount × exchange rate)
    &quot;remitcurrency&quot;: &quot;USD&quot;, // remittance currency
    &quot;payoutCurrency&quot;: &quot;BDT&quot;, // payout currency 
    &quot;payoutIso&quot;: &quot;BG&quot;, // payout Iso 2  char aplha 
    &quot;bankId&quot;: &quot;BGDAB&quot;, // payout location ID
    &quot;remittertype&quot;: &quot;I&quot;, // remitter type (I = Individual, C = Company)
    &quot;senderfirstname&quot;: &quot;Dinesh&quot;, // sender&#x27;s first name
    &quot;sendermiddlename&quot;: &quot;&quot;, // sender&#x27;s middle name
    &quot;senderlastname&quot;: &quot;Patil&quot;, // sender&#x27;s last name
    &quot;sendergender&quot;: &quot;Male&quot;, // sender&#x27;s gender
    &quot;senderaddress&quot;: &quot;Softieons Abhinandan royal Surat Gujarat 395007, India&quot;, // sender&#x27;s address
    &quot;sendercity&quot;: &quot;India&quot;, // sender&#x27;s city
    &quot;senderstate&quot;: &quot;Gujarat&quot;, // sender&#x27;s state
    &quot;senderzipcode&quot;: &quot;395854&quot;, // sender&#x27;s postal/ZIP code
    &quot;fromCountry&quot;: &quot;IN&quot;, // sender&#x27;s country code
    &quot;sendercountry&quot;: &quot;IND&quot;, // sender&#x27;s country code
    &quot;sendermobile&quot;: &quot;917507642090&quot;, // sender&#x27;s mobile number
    &quot;sendernationality&quot;: &quot;IND&quot;, // sender&#x27;s nationality
    &quot;senderidtype&quot;: &quot;01&quot;, // sender&#x27;s ID type code
    &quot;senderidtyperemarks&quot;: &quot;Passport&quot;, // sender&#x27;s ID type description
    &quot;senderidnumber&quot;: &quot;754678&quot;, // sender&#x27;s ID number
    &quot;senderidissuecountry&quot;: &quot;IND&quot;, // sender&#x27;s ID issuing country
    &quot;senderidissuedate&quot;: &quot;2025-08-12&quot;, // sender&#x27;s ID issue date (YYYY-MM-DD)
    &quot;senderidexpiredate&quot;: &quot;2025-08-30&quot;, // sender&#x27;s ID expiry date (YYYY-MM-DD)
    &quot;senderdateofbirth&quot;: &quot;1995-08-04&quot;, // sender&#x27;s date of birth (YYYY-MM-DD)
    &quot;senderoccupation&quot;: &quot;07&quot;, // sender&#x27;s occupation code
    &quot;senderoccupationremarks&quot;: &quot;Agriculture forestry fisheries&quot;, // sender&#x27;s occupation description
    &quot;sendersourceoffund&quot;: &quot;01&quot;, // source of funds code
    &quot;sendersourceoffundremarks&quot;: &quot;Salary to include any work related compensation and pensions&quot;, // source of funds description
    &quot;senderemail&quot;: &quot;dinesh.softieons@gmail.com&quot;, // sender&#x27;s email address
    &quot;sendernativefirstname&quot;: &quot;&quot;, // sender&#x27;s native language first name
    &quot;senderbeneficiaryrelationship&quot;: &quot;&quot;, // relationship to beneficiary code
    &quot;senderbeneficiaryrelationshipremarks&quot;: &quot;&quot;, // relationship to beneficiary description
    &quot;purposeofremittance&quot;: &quot;16&quot;, // purpose of remittance code
    &quot;purposeofremittanceremark&quot;: &quot;&quot;, // purpose of remittance description
    &quot;beneficiarytype&quot;: &quot;I&quot;, // beneficiary type (I = Individual, C = Company)
    &quot;receiverfirstname&quot;: &quot;Ebrahim&quot;, // receiver&#x27;s first name
    &quot;receivermiddlename&quot;: &quot;&quot;, // receiver&#x27;s middle name
    &quot;receiverlastname&quot;: &quot;kadir&quot;, // receiver&#x27;s last name
    &quot;receiveraddress&quot;: &quot;&quot;, // receiver&#x27;s address
    &quot;receivercontactnumber&quot;: &quot;&quot;, // receiver&#x27;s contact number
    &quot;receiverstate&quot;: &quot;&quot;, // receiver&#x27;s state
    &quot;receiverareatown&quot;: &quot;&quot;, // receiver&#x27;s area or town
    &quot;receivercity&quot;: &quot;&quot;, // receiver&#x27;s city
    &quot;receiverzipcode&quot;: &quot;&quot;, // receiver&#x27;s postal/ZIP code
    &quot;receivercountry&quot;: &quot;BGD&quot;, // receiver&#x27;s country code
    &quot;receiveridtype&quot;: &quot;&quot;, // receiver&#x27;s ID type code
    &quot;receiveridtyperemarks&quot;: &quot;&quot;, // receiver&#x27;s ID type description
    &quot;receiveroccupation&quot;: &quot;&quot;, // receiver&#x27;s occupation code
    &quot;receiveroccupationremark&quot;: &quot;&quot;, // receiver&#x27;s occupation description
    &quot;receiveridnumber&quot;: &quot;&quot;, // receiver&#x27;s ID number
    &quot;receiveremail&quot;: &quot;&quot;, // receiver&#x27;s email address
    &quot;receivernativefirstname&quot;: &quot;&quot;, // receiver&#x27;s native language first name
    &quot;receivernativemiddlename&quot;: &quot;&quot;, // receiver&#x27;s native language middle name
    &quot;receivernativelastname&quot;: &quot;&quot;, // receiver&#x27;s native language last name
    &quot;receivernativeaddress&quot;: &quot;&quot;, // receiver&#x27;s native language address
    &quot;sendersecondaryidtype&quot;: &quot;01&quot;, // sender&#x27;s secondary ID type code
    &quot;sendersecondaryidnumber&quot;: &quot;754678&quot;, // sender&#x27;s secondary ID number
    &quot;sendernativelastname&quot;: &quot;&quot;, // sender&#x27;s native language last name
    &quot;calcby&quot;: &quot;P&quot;, // calculation method (P = principal, R = remittance)
    &quot;paymentmode&quot;: &quot;B&quot;, // payment mode (B = bank, C = cash)
    &quot;bankName&quot;: &quot;AB BANK&quot;, // bank name
    &quot;bankbranchname&quot;: &quot;&quot;, // bank branch name
    &quot;bankbranchcode&quot;: &quot;7778asdasd&quot;, // bank branch code
    &quot;bankaccountnumber&quot;: &quot;100801463700&quot;, // bank account number
    &quot;swiftcode&quot;: &quot;&quot;, // SWIFT/BIC code
    &quot;promotioncode&quot;: &quot;&quot;, // promotion code
    &quot;sendernativeaddress&quot;: &quot;&quot;, // sender&#x27;s native language address
    &quot;receivernationality&quot;: &quot;BGD&quot;, // receiver&#x27;s nationality
    &quot;receiveridissuedate&quot;: &quot;&quot;, // receiver&#x27;s ID issue date
    &quot;receiveridexpiredate&quot;: &quot;&quot;, // receiver&#x27;s ID expiry date
    &quot;receiverdistrict&quot;: &quot;&quot;, // receiver&#x27;s district
    &quot;receiptcpf&quot;: &quot;&quot;, // CPF number for Brazil (if applicable)
    &quot;notes&quot;: &quot;test&quot;, // notes
    &quot;receiverdateofbirth&quot;: &quot;&quot;, // receiver&#x27;s date of birth
    &quot;receivergender&quot;: &quot;&quot;, // receiver&#x27;s gender
    &quot;receiveraccounttype&quot;: &quot;&quot; // receiver&#x27;s account type
}
</code></pre>
            <h4>Example Responses</h4><b>Success</b><pre><code>{
  &quot;status&quot;: true,
  &quot;message&quot;: &quot;Create Transaction executed successfully&quot;,
  &quot;data&quot;: {
    &quot;example&quot;: &quot;value&quot;
  }
}</code></pre><b>Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;An error occurred&quot;,
  &quot;error_code&quot;: &quot;ERROR_CODE&quot;,
  &quot;data&quot;: null
}</code></pre><b>Validation Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;Validation Failed&quot;,
  &quot;error_code&quot;: &quot;VALIDATION_ERROR&quot;,
  &quot;data&quot;: {
    &quot;field_name&quot;: [
      &quot;The field_name field is required.&quot;
    ]
  }
}</code></pre>
          </div>
        </div>
        <h3 id='Transfer Mobile Money'>#Transfer Mobile Money</h3>
        <div class="endpoint" data-name="country list" data-url="{{ $credential->api_url }}/transfer-money/country-list" data-method="GET">
          <div class="head" onclick="this.parentElement.classList.toggle('open')">
            <span class="method">GET</span>
            <div class="url">{{ $credential->api_url }}/transfer-money/country-list</div>
            <span class="pill">Country List</span>
          </div>
          <div class="body">
            
            <h4>Headers</h4>
            <table><thead><tr><th>Header</th><th>Value</th><th>Notes</th></tr></thead><tbody><tr><td><code>Content-Type</code></td><td><code>application/json</code></td><td></td></tr></tbody></table>
            
            
            <h4>Example Responses</h4><b>Success</b><pre><code>{
  &quot;status&quot;: true,
  &quot;message&quot;: &quot;Country List executed successfully&quot;,
  &quot;data&quot;: {
    &quot;example&quot;: &quot;value&quot;
  }
}</code></pre><b>Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;An error occurred&quot;,
  &quot;error_code&quot;: &quot;ERROR_CODE&quot;,
  &quot;data&quot;: null
}</code></pre><b>Validation Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;Validation Failed&quot;,
  &quot;error_code&quot;: &quot;VALIDATION_ERROR&quot;,
  &quot;data&quot;: {
    &quot;field_name&quot;: [
      &quot;The field_name field is required.&quot;
    ]
  }
}</code></pre>
          </div>
        </div>
        
        <div class="endpoint" data-name="fields" data-url="{{ $credential->api_url }}/transfer-money/get-fields" data-method="GET">
          <div class="head" onclick="this.parentElement.classList.toggle('open')">
            <span class="method">GET</span>
            <div class="url">{{ $credential->api_url }}/transfer-money/get-fields</div>
            <span class="pill">fields</span>
          </div>
          <div class="body">
            
            <h4>Headers</h4>
            <table><thead><tr><th>Header</th><th>Value</th><th>Notes</th></tr></thead><tbody><tr><td><code>Content-Type</code></td><td><code>application/json</code></td><td></td></tr><tr><td><code>Accept</code></td><td><code>application/json</code></td><td></td></tr></tbody></table>
            
            
            <h4>Example Responses</h4><b>Success</b><pre><code>{
  &quot;status&quot;: true,
  &quot;message&quot;: &quot;fields executed successfully&quot;,
  &quot;data&quot;: {
    &quot;example&quot;: &quot;value&quot;
  }
}</code></pre><b>Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;An error occurred&quot;,
  &quot;error_code&quot;: &quot;ERROR_CODE&quot;,
  &quot;data&quot;: null
}</code></pre><b>Validation Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;Validation Failed&quot;,
  &quot;error_code&quot;: &quot;VALIDATION_ERROR&quot;,
  &quot;data&quot;: {
    &quot;field_name&quot;: [
      &quot;The field_name field is required.&quot;
    ]
  }
}</code></pre>
          </div>
        </div>
        
        <div class="endpoint" data-name="create transaction" data-url="{{ $credential->api_url }}/transfer-money/create-transaction" data-method="POST">
          <div class="head" onclick="this.parentElement.classList.toggle('open')">
            <span class="method">POST</span>
            <div class="url">{{ $credential->api_url }}/transfer-money/create-transaction</div>
            <span class="pill">Create Transaction</span>
          </div>
          <div class="body">
            
            <h4>Headers</h4>
            <table><thead><tr><th>Header</th><th>Value</th><th>Notes</th></tr></thead><tbody><tr><td><code>Content-Type</code></td><td><code>application/json</code></td><td></td></tr></tbody></table>
            <h4>Parameters</h4><table><thead><tr><th>Name</th><th>Type</th><th>Example</th></tr></thead><tbody><tr><td><code>amount</code></td><td>str</td><td><code>2.00</code></td></tr><tr><td><code>exchange_rate_id</code></td><td>int</td><td><code>75</code></td></tr><tr><td><code>exchange_rate</code></td><td>str</td><td><code>634.651875</code></td></tr><tr><td><code>converted_amount</code></td><td>str</td><td><code>1269.30375</code></td></tr><tr><td><code>payoutCurrency</code></td><td>str</td><td><code>XOF</code></td></tr><tr><td><code>channel_name</code></td><td>str</td><td><code>Orange</code></td></tr><tr><td><code>sender_mobile</code></td><td>str</td><td><code>917507642051</code></td></tr><tr><td><code>sender_country_code</code></td><td>str</td><td><code>IN</code></td></tr><tr><td><code>sender_name</code></td><td>str</td><td><code>Dinesh</code></td></tr><tr><td><code>sender_surname</code></td><td>str</td><td><code>Patil</code></td></tr><tr><td><code>sender_address</code></td><td>str</td><td><code>Softieons Abhinandan royal Surat Gujarat 395007, India</code></td></tr><tr><td><code>sender_city</code></td><td>str</td><td><code>India</code></td></tr><tr><td><code>sender_state</code></td><td>str</td><td><code>Gujarat</code></td></tr><tr><td><code>sender_postalCode</code></td><td>str</td><td><code>395007</code></td></tr><tr><td><code>sender_email</code></td><td>str</td><td><code>dinesh.softieons@gmail.com</code></td></tr><tr><td><code>sender_dateOfBirth</code></td><td>NoneType</td><td><code></code></td></tr><tr><td><code>sender_document</code></td><td>NoneType</td><td><code></code></td></tr><tr><td><code>sender_placeofbirth</code></td><td>str</td><td><code>2021-08-04</code></td></tr><tr><td><code>recipient_mobile</code></td><td>str</td><td><code>221776161358</code></td></tr><tr><td><code>recipient_country_code</code></td><td>str</td><td><code>SN</code></td></tr><tr><td><code>recipient_name</code></td><td>str</td><td><code>Sunny</code></td></tr><tr><td><code>recipient_surname</code></td><td>str</td><td><code>Nilhani</code></td></tr><tr><td><code>recipient_address</code></td><td>str</td><td><code></code></td></tr><tr><td><code>recipient_city</code></td><td>str</td><td><code></code></td></tr><tr><td><code>recipient_state</code></td><td>str</td><td><code></code></td></tr><tr><td><code>recipient_postalcode</code></td><td>str</td><td><code></code></td></tr><tr><td><code>recipient_email</code></td><td>NoneType</td><td><code></code></td></tr><tr><td><code>recipient_dateofbirth</code></td><td>str</td><td><code></code></td></tr><tr><td><code>recipient_document</code></td><td>NoneType</td><td><code></code></td></tr><tr><td><code>recipient_destinationAccount</code></td><td>NoneType</td><td><code></code></td></tr><tr><td><code>purposeOfTransfer</code></td><td>str</td><td><code>Agriculture forestry fisheries</code></td></tr><tr><td><code>sourceOfFunds</code></td><td>str</td><td><code>Salary to include any work related compensation and pensions</code></td></tr><tr><td><code>notes</code></td><td>str</td><td><code>Salary to include any work related compensation and pensions</code></td></tr></tbody></table>
            <h4>Request Body</h4><pre><code>{
    &quot;amount&quot;: &quot;2.00&quot;,
    &quot;exchange_rate_id&quot;: 75,
    &quot;exchange_rate&quot;: &quot;634.651875&quot;,
    &quot;converted_amount&quot;: &quot;1269.30375&quot;,
    &quot;payoutCurrency&quot;: &quot;XOF&quot;,
    &quot;channel_name&quot;: &quot;Orange&quot;,
    &quot;sender_mobile&quot;: &quot;917507642051&quot;,
    &quot;sender_country_code&quot;: &quot;IN&quot;,
    &quot;sender_name&quot;: &quot;Dinesh&quot;,
    &quot;sender_surname&quot;: &quot;Patil&quot;,
    &quot;sender_address&quot;: &quot;Softieons Abhinandan royal Surat Gujarat 395007, India&quot;,
    &quot;sender_city&quot;: &quot;India&quot;,
    &quot;sender_state&quot;: &quot;Gujarat&quot;,
    &quot;sender_postalCode&quot;: &quot;395007&quot;,
    &quot;sender_email&quot;: &quot;dinesh.softieons@gmail.com&quot;,
    &quot;sender_dateOfBirth&quot;: null,
    &quot;sender_document&quot;: null,
    &quot;sender_placeofbirth&quot;: &quot;2021-08-04&quot;,
    &quot;recipient_mobile&quot;: &quot;221776161358&quot;,
    &quot;recipient_country_code&quot;: &quot;SN&quot;,
    &quot;recipient_name&quot;: &quot;Sunny&quot;,
    &quot;recipient_surname&quot;: &quot;Nilhani&quot;,
    &quot;recipient_address&quot;: &quot;&quot;,
    &quot;recipient_city&quot;: &quot;&quot;,
    &quot;recipient_state&quot;: &quot;&quot;,
    &quot;recipient_postalcode&quot;: &quot;&quot;,
    &quot;recipient_email&quot;: null,
    &quot;recipient_dateofbirth&quot;: &quot;&quot;,
    &quot;recipient_document&quot;: null,
    &quot;recipient_destinationAccount&quot;: null,
    &quot;purposeOfTransfer&quot;: &quot;Agriculture forestry fisheries&quot;,
    &quot;sourceOfFunds&quot;: &quot;Salary to include any work related compensation and pensions&quot;,
    &quot;notes&quot;: &quot;Salary to include any work related compensation and pensions&quot;
}</code></pre>
            <h4>Example Responses</h4><b>Success</b><pre><code>{
  &quot;status&quot;: true,
  &quot;message&quot;: &quot;Create Transaction executed successfully&quot;,
  &quot;data&quot;: {
    &quot;example&quot;: &quot;value&quot;
  }
}</code></pre><b>Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;An error occurred&quot;,
  &quot;error_code&quot;: &quot;ERROR_CODE&quot;,
  &quot;data&quot;: null
}</code></pre><b>Validation Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;Validation Failed&quot;,
  &quot;error_code&quot;: &quot;VALIDATION_ERROR&quot;,
  &quot;data&quot;: {
    &quot;field_name&quot;: [
      &quot;The field_name field is required.&quot;
    ]
  }
}</code></pre>
          </div>
        </div>
        <h3 id='Webhooks'>#Webhooks</h3>
        <div class="endpoint" data-name="register webhook" data-url="{{ $credential->api_url }}/webhook/register" data-method="POST">
          <div class="head" onclick="this.parentElement.classList.toggle('open')">
            <span class="method">POST</span>
            <div class="url">{{ $credential->api_url }}/webhook/register</div>
            <span class="pill">Register Webhook</span>
          </div>
          <div class="body">
            
            <h4>Headers</h4>
            <table><thead><tr><th>Header</th><th>Value</th><th>Notes</th></tr></thead><tbody><tr><td><code>Content-Type</code></td><td><code>application/json</code></td><td></td></tr></tbody></table>
            
            <h4>Request Body</h4><pre><code>{
    &quot;callback_url&quot;: &quot;https://geo.travelieons.com/api-credentials&quot;
}</code></pre>
            <h4>Example Responses</h4><b>Success</b><pre><code>{
  &quot;status&quot;: true,
  &quot;message&quot;: &quot;Register Webhook executed successfully&quot;,
  &quot;data&quot;: {
    &quot;example&quot;: &quot;value&quot;
  }
}</code></pre><b>Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;An error occurred&quot;,
  &quot;error_code&quot;: &quot;ERROR_CODE&quot;,
  &quot;data&quot;: null
}</code></pre><b>Validation Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;Validation Failed&quot;,
  &quot;error_code&quot;: &quot;VALIDATION_ERROR&quot;,
  &quot;data&quot;: {
    &quot;field_name&quot;: [
      &quot;The field_name field is required.&quot;
    ]
  }
}</code></pre>
          </div>
        </div>
        
        <div class="endpoint" data-name="delete webhook" data-url="{{ $credential->api_url }}/webhook/delete" data-method="POST">
          <div class="head" onclick="this.parentElement.classList.toggle('open')">
            <span class="method">POST</span>
            <div class="url">{{ $credential->api_url }}/webhook/delete</div>
            <span class="pill">Delete Webhook</span>
          </div>
          <div class="body">
            
            <h4>Headers</h4>
            <table><thead><tr><th>Header</th><th>Value</th><th>Notes</th></tr></thead><tbody><tr><td><code>Content-Type</code></td><td><code>application/json</code></td><td></td></tr></tbody></table>
            <h4>Parameters</h4><table><thead><tr><th>Name</th><th>Type</th><th>Example</th></tr></thead><tbody><tr><td><code>secret</code></td><td>str</td><td><code>tLqhK20NiR0n406vtydBN4mWy2mNbrlvbENbyS35</code></td></tr></tbody></table>
            <h4>Request Body</h4><pre><code>{
    &quot;secret&quot;: &quot;tLqhK20NiR0n406vtydBN4mWy2mNbrlvbENbyS35&quot;
}</code></pre>
            <h4>Example Responses</h4><b>Success</b><pre><code>{
  &quot;status&quot;: true,
  &quot;message&quot;: &quot;Delete Webhook executed successfully&quot;,
  &quot;data&quot;: {
    &quot;example&quot;: &quot;value&quot;
  }
}</code></pre><b>Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;An error occurred&quot;,
  &quot;error_code&quot;: &quot;ERROR_CODE&quot;,
  &quot;data&quot;: null
}</code></pre><b>Validation Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;Validation Failed&quot;,
  &quot;error_code&quot;: &quot;VALIDATION_ERROR&quot;,
  &quot;data&quot;: {
    &quot;field_name&quot;: [
      &quot;The field_name field is required.&quot;
    ]
  }
}</code></pre>
          </div>
        </div>
        <h3 id='General'>#General</h3>
        <div class="endpoint" data-name="exchnage rate list" data-url="{{ $credential->api_url }}/exchange-rate" data-method="POST">
          <div class="head" onclick="this.parentElement.classList.toggle('open')">
            <span class="method">POST</span>
            <div class="url">{{ $credential->api_url }}/exchange-rate</div>
            <span class="pill">Exchnage Rate List</span>
          </div>
          <div class="body">
            
            <h4>Headers</h4>
            <table><thead><tr><th>Header</th><th>Value</th><th>Notes</th></tr></thead><tbody><tr><td><code>Content-Type</code></td><td><code>application/json</code></td><td></td></tr></tbody></table>
            <h4>Parameters</h4><table><thead><tr><th>Name</th><th>Type</th><th>Example</th></tr></thead><tbody><tr><td><code>payoutCurrency</code></td><td>str</td><td><code>BDT</code></td></tr><tr><td><code>service</code></td><td>int</td><td><code>1</code></td></tr></tbody></table>
            <h4>Request Body</h4><pre><code>{
    &quot;payoutCurrency&quot;: &quot;BDT&quot;,
    &quot;service&quot;: 1
}</code></pre>
            <h4>Example Responses</h4><b>Success</b><pre><code>{
  &quot;status&quot;: true,
  &quot;message&quot;: &quot;Exchnage Rate List executed successfully&quot;,
  &quot;data&quot;: {
    &quot;example&quot;: &quot;value&quot;
  }
}</code></pre><b>Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;An error occurred&quot;,
  &quot;error_code&quot;: &quot;ERROR_CODE&quot;,
  &quot;data&quot;: null
}</code></pre><b>Validation Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;Validation Failed&quot;,
  &quot;error_code&quot;: &quot;VALIDATION_ERROR&quot;,
  &quot;data&quot;: {
    &quot;field_name&quot;: [
      &quot;The field_name field is required.&quot;
    ]
  }
}</code></pre>
          </div>
        </div>
        
        <div class="endpoint" data-name="get transaction status" data-url="{{ $credential->api_url }}/get-transaction-status" data-method="POST">
          <div class="head" onclick="this.parentElement.classList.toggle('open')">
            <span class="method">POST</span>
            <div class="url">{{ $credential->api_url }}/get-transaction-status</div>
            <span class="pill">Get Transaction Status</span>
          </div>
          <div class="body">
            
            <h4>Headers</h4>
            <table><thead><tr><th>Header</th><th>Value</th><th>Notes</th></tr></thead><tbody><tr><td><code>Content-Type</code></td><td><code>application/json</code></td><td></td></tr></tbody></table>
            <h4>Parameters</h4><table><thead><tr><th>Name</th><th>Type</th><th>Example</th></tr></thead><tbody><tr><td><code>thirdPartyId</code></td><td>str</td><td><code>GPTM-1-1755236774</code></td></tr></tbody></table>
            <h4>Request Body</h4><pre><code>{
    &quot;thirdPartyId&quot;: &quot;GPTM-1-1755236774&quot;
}</code></pre>
            <h4>Example Responses</h4><b>Success</b><pre><code>{
  &quot;status&quot;: true,
  &quot;message&quot;: &quot;Get Transaction Status executed successfully&quot;,
  &quot;data&quot;: {
    &quot;example&quot;: &quot;value&quot;
  }
}</code></pre><b>Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;An error occurred&quot;,
  &quot;error_code&quot;: &quot;ERROR_CODE&quot;,
  &quot;data&quot;: null
}</code></pre><b>Validation Error</b><pre><code>{
  &quot;status&quot;: false,
  &quot;message&quot;: &quot;Validation Failed&quot;,
  &quot;error_code&quot;: &quot;VALIDATION_ERROR&quot;,
  &quot;data&quot;: {
    &quot;field_name&quot;: [
      &quot;The field_name field is required.&quot;
    ]
  }
}</code></pre>
          </div>
        </div>
        
    </section>
    <footer>© Geopay – Merchant API. Generated from your Postman collection.</footer>
  </main>
</div>
<script>
  const q = document.getElementById('search');
q.addEventListener('input', e => {
    const s = e.target.value.toLowerCase();
    let firstMatch = null;

    document.querySelectorAll('.endpoint').forEach(ep => {
        const hay = [ep.dataset.name, ep.dataset.url, ep.dataset.method].join(' ').toLowerCase();
        if (hay.includes(s)) {
            ep.style.display = '';
            if (!firstMatch) firstMatch = ep; // store first match
        } else {
            ep.style.display = 'none';
        }
    });

    // Scroll to the first match
    if (firstMatch) {
        firstMatch.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});

</script>
</body>
</html>
