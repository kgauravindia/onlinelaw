<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta Tag -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
    // Set include path to root directory so that relative requires in includes/header.php and function.php work correctly
    set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__DIR__));
    require_once('includes/config.php');

    // Since this script is in a subdirectory, if we are on localhost, $base_url will end in '/bank/'
    // We need to strip the '/bank/' from $base_url to correctly point to the root path
    if (isset($base_url)) {
        $base_url = preg_replace('/bank\/$/', '', $base_url);
    }

    require_once ('includes/function.php'); 
    
    $page_title = "All Indian Banks Official Contact Details Directory | Online Law";
    $meta_desc = "Find official websites, toll-free customer care numbers, support email addresses, and mobile banking channels for all major banks in India. Verified directory.";
?>
<meta name="description" content="<?php echo $meta_desc; ?>">
<meta name="keywords" content="bank customer care numbers, toll free banking support, bank official websites, bank email list, sbi support email, hdfc toll free helpline">
<meta name="author" content="OfferPlant">
<title><?php echo $page_title; ?></title>

<link rel="stylesheet" href="<?php echo $base_url; ?>css/bank.css">

<?php require_once ('includes/header.php'); ?> 

<!-- Banner Section Start -->
<section class="bg-img-3 overlay p-0 full-row" style="padding: 20px 0 !important;">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="banner-text text-center">
					<h2 class="text-white banner-tittle position-relative mb-1" style="font-family: var(--font-heading) !important; font-size: 32px; margin: 0;">Bank Contact Details</h2>
					<ul class="list-inline mt-2 mb-0" style="color: #cbd5e1; font-family: var(--font-body); font-size: 15px;">
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>bank" style="color: rgba(255,255,255,0.8); text-decoration: none;">Bank Directory</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item" style="color: #fff; font-weight: 500;">Contact Details</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Banner Section End --> 

<section class="full-row bg-body py-80" style="padding: 60px 0;">
	<div class="container">
		

		<!-- Unified Bank Directory Table -->
		<div class="row mb-5">
			<div class="col-lg-12">
				<div class="detail-area p-3 p-md-5" style="border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); background: #fff;">
					<div class="table-responsive mt-2">
    <div class="text-center mb-4">
        <h3 style="font-family: var(--font-heading); font-weight: 700; margin-bottom: 10px;">All Indian Banks Official Contact Directory</h3>
        <p style="color: var(--text-muted); font-size: 15px; margin-bottom: 0;">A verified directory of official websites, toll-free numbers, and support emails for all major Indian banks.</p>
    </div>
    <table class="table table-bordered table-striped" id="bankDataTable">
        <thead class="bg-primary text-white">
            <tr>
                <th>SR NO</th>
                <th>BANK NAME</th>
                <th>ADDRESS</th>
                <th>WEBSITE</th>
                <th>TOLL FREE</th>
                <th>EMAIL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td><b>Akhand Anand Co-Operative Bank</b></td>
                <td>2ND AND 3RD FLOOR,MAHAVIR SHOPPING CENTRE,AMROLI ROAD,KATARGAM,SURAT</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>2</td>
                <td><b>ABHYUDAYA CO-OPERATIVE BANK LTD</b></td>
                <td>K. K. Tower, Abhyudaya Bank Lane, <br />
Off. G. D. Ambekar Marg, <br />
Parel Village, <br />
MUMBAI - 400 012</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>3</td>
                <td><b>NATWEST MARKETS PLC</b></td>
                <td>414, Empire Complex,<br />
Senapati Bapat Marg, <br />
Lower Parel (West), <br />
Mumbai 400 013</td>
                <td><a href="https://www.natwestmarkets.bank.in/index.html" target="_blank" class="text-primary font-weight-bold">natwestmarkets.bank.in/inde...</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>4</td>
                <td><b>The Adarsh Cooperative Urban Bank Limited</b></td>
                <td>1-251/1, MPR Complex, Phase - 1,<br />
IDA, Shapurnagar, Jeedimetla<br />
Medchal- Malkajgiri, Hyderabad,<br />
Telangana - 500055</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>5</td>
                <td><b>THE AKOLA DISTRICT CENTRAL CO-OPERATIVE BANK LTD.</b></td>
                <td>Post Box no. 8, <br />
Civil Line,<br />
Akola 444001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>6</td>
                <td><b>THE AHMEDNAGAR DISTRICT CENTRAL CO-OPERATIVE BANK</b></td>
                <td>HEAD OFFICE, STATION ROAD, AHMEDNAGAR - 414001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>7</td>
                <td><b>AIRTEL PAYMENTS BANK LTD.</b></td>
                <td>BHARTI CRESCENT, 1, NELSON MANDELA ROAD, VASANT KUNJ, PHASE II, NEW DELHI-110070.</td>
                <td><a href="https://www.airtelpayments.bank.in" target="_blank" class="text-primary font-weight-bold">airtelpayments.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>8</td>
                <td><b>THE AJARA URBAN COOP BANK LTD</b></td>
                <td>393 B, MAIN ROAD AJARA, TAL- AJARA, DIST. KOLHAPUR<br />
PIN- 416505</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>9</td>
                <td><b>AMBARNATH JAI-HIND CO-OP. BANK LTD.</b></td>
                <td>PLOT NO. 42, LOKMANYA TILAK ROAD, AMBARNATH (W), DIST. THANE - 421 501</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>10</td>
                <td><b>THE AKOLA JANATA COMMERCIAL COOPERATIVE BANK LTD</b></td>
                <td>JANVAIBHAV&quot;  OLD COTTON MARKET AKOLA  MAHARASHTRA 444001 &quot;</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>11</td>
                <td><b>THE AHMEDABAD MERCANTILE CO-OPERATIVE BANK LTD.</b></td>
                <td>H.O., AMCO HOUSE&quot; Nr. Stadium Circle Navrangpura Ahmedabad - 380009&quot;</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>12</td>
                <td><b>AHMEDNAGAR MERCHANT&#039;S CO-OP. BANK LTD. AHMEDNAGAR</b></td>
                <td>PLOT NO. 33, MARKET YARD, STATION ROAD, AHMEDNAGAR - 414 001.</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>13</td>
                <td><b>AUSTRALIA AND NEW ZEALAND BANKING GROUP LIMITED</b></td>
                <td>Cnergy, 6th Floor, <br />
Appasaheb Marathe Marg, <br />
Prabhadevi, <br />
Mumbai - 400 025</td>
                <td><a href="http://www.anz.com/india/en/Corporate/" target="_blank" class="text-primary font-weight-bold">anz.com/india/en/Corporate</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>14</td>
                <td><b>ANDHRA PRADESH STATE CO-OPERATIVE BANK LTD.</b></td>
                <td>Head Office, PB 142, <br />
Troop Bazar,<br />
Hyderabad 500001</td>
                <td><a href="https://www.apgb.bank.in/" target="_blank" class="text-primary font-weight-bold">apgb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>15</td>
                <td><b>THE A.P. MAHESH CO-OPERATIVE URBAN BANK LTD</b></td>
                <td>5-3-989, SHERZA ESTATE, <br />
N.S.ROAD, OSMANGUNJ, <br />
HYDERABAD - 500095</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>16</td>
                <td><b>ARVIND SAHAKARI BANK LTD</b></td>
                <td>Main Road, Katol<br />
District - Nagpur<br />
441302</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>17</td>
                <td><b>APNA SAHAKARI BANK LTD.</b></td>
                <td>Apna Bank Bhavan, <br />
Dr.S.S.Rao Road, <br />
Parel, Mumbai 400012</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>18</td>
                <td><b>AU SMALL FINANCE BANK LIMITED</b></td>
                <td>AU FINANCIERS(INDIA) LIMITED,<br />
19-A, DHULESHWAR GARDEN,<br />
AJMER ROAD,<br />
JAIPUR - 302001</td>
                <td><a href="https://www.au.bank.in/" target="_blank" class="text-primary font-weight-bold">au.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>19</td>
                <td><b>ALMORA URBAN CO-OPERATIVE BANK LTD.</b></td>
                <td>Gururani Complex, <br />
Lala Bazar, <br />
Almora -263601</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>20</td>
                <td><b>BASSEIN CATHOLIC CO-OPERATIVE BANK LTD.</b></td>
                <td>Catholic Bank Bldg. <br />
Papdy, Vasai, <br />
Dist : Thane 401207</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>21</td>
                <td><b>THE BARAMATI SAHAKARI BANK LIMITED</b></td>
                <td>BHIGWAN ROAD, BARAMATI,<br />
PUNE-413102</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>22</td>
                <td><b>BANK OF BARODA</b></td>
                <td>Baroda Sun Tower,<br />
C-34, G Block,<br />
BKC, MUMBAI-400051</td>
                <td><a href="https://www.bankofbaroda.in" target="_blank" class="text-primary font-weight-bold">bankofbaroda.in</a><br>
<a href="https://bob.bank.in" target="_blank" class="text-success small"><i class="fas fa-shield-alt"></i> bob.bank.in</a></td>
                <td><a href="tel:18002584455">1800 258 4455</a><br><a href="tel:18001024455">1800 102 4455</a></td>
                <td><a href="mailto:customercare@bankofbaroda.com">customercare@bankofbaroda.com</a></td>
            </tr>
            <tr>
                <td>23</td>
                <td><b>BARCLAYS BANK PLC</b></td>
                <td>801/808, Ceejay House, <br />
Shivsagar Estate, <br />
Dr. Annie Beasant Road, <br />
Worli, Mumbai - 400 018</td>
                <td><a href="https://www.barclays.bank.in/" target="_blank" class="text-primary font-weight-bold">barclays.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>24</td>
                <td><b>BANK OF BAHRAIN &amp; KUWAIT B S C</b></td>
                <td>225, JOLLY MAKERS CHAMBERS II,<br />
GR FLR, NARIMAN POINT <br />
MUMBAI- 400021</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>25</td>
                <td><b>BHARAT COOPERATIVE BANK MUMBAI LIMITED</b></td>
                <td>Marutagiri, Sonawala Road, <br />
Goregaon(E), <br />
Mumbai - 400 063</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>26</td>
                <td><b>BANK OF CEYLON</b></td>
                <td>No. 20/21, CASA Major Road, <br />
2/11 Zerat Garden, <br />
Egmore, <br />
Chennai-600008</td>
                <td><a href="https://www.boc.lk/" target="_blank" class="text-primary font-weight-bold">boc.lk</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>27</td>
                <td><b>BANK OF CHINA LIMITED INDIA Branch</b></td>
                <td>41-B, 4TH FLOOR, 4TH NORTH AVENUE, MAKER MAXITY, BKC, BANDRA EAST MUMBAI 400051</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>28</td>
                <td><b>BANDHAN BANK LIMITED</b></td>
                <td>DN 32<br />
SECTOR V<br />
SALT LAKE CITY<br />
KOLKATA<br />
700091</td>
                <td><a href="https://www.bandhan.bank.in/" target="_blank" class="text-primary font-weight-bold">bandhan.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>29</td>
                <td><b>BANK OF INDIA</b></td>
                <td>Bank of India Head Office Information Technology Department Star House C -5, G - Block, 8th Floor Bandra - Kurla Complex 400 051 / Bandra (East), Mumbai 400 051</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>30</td>
                <td><b>BOMBAY MERCANTILE COOPERATIVE BANK LTD</b></td>
                <td>ZAIN G RANGOONWALA BUILDING,78,MOHAMEDALI ROAD, MUMBAI-400003</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>31</td>
                <td><b>BNP PARIBAS</b></td>
                <td>BNP Paribas House, 1 North Avenue, <br />
Maker Maxity, Bandra Kurla Complex, <br />
Bandra East, Mumbai 400051</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>32</td>
                <td><b>BHAGINI NIVEDITA SAHAKARI BANK LTD, PUNE</b></td>
                <td>NIVEDITA BHAVAN, CTS.NO. 34/7,<br />
FINAL PLOT NO. 35/7B, PRABHAT ROAD, LANE NO 8, ERANDWANE, PUNE 411004</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>33</td>
                <td><b>BANK OF AMERICA</b></td>
                <td>EXPRESS TOWERS, <br />
NARIMAN POINT, <br />
MUMBAI 400 021</td>
                <td><a href="http://bofa-india.com/" target="_blank" class="text-primary font-weight-bold">bofa-india.com</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>34</td>
                <td><b>MUFG Bank, Ltd.</b></td>
                <td>Jeevan Vihar Building, <br />
3 Parliament Street, <br />
New Delhi - 110001</td>
                <td><a href="http://www.bk.mufg.jp/global/globalnetwork/asiaoceania/mumbai.html" target="_blank" class="text-primary font-weight-bold">bk.mufg.jp/global/globalnet...</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>35</td>
                <td><b>THE BURDWAN CENTRAL COOPERATIVE BANK LTTD.</b></td>
                <td>Head Office, 64 G T Road and Dist, Purba Bardhaman. Pin Code - 713101</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>36</td>
                <td><b>CENTRAL BANK OF INDIA</b></td>
                <td>FUNDS MANAGEMENT DEPTT., <br />
5TH FLOOR, CHANDERMUKHI BLDG., <br />
NARIMAN POINT, MUMBAI</td>
                <td><a href="https://www.centralbankofindia.co.in" target="_blank" class="text-primary font-weight-bold">centralbankofindia.co.in</a><br>
<a href="https://centralbank.bank.in" target="_blank" class="text-success small"><i class="fas fa-shield-alt"></i> centralbank.bank.in</a></td>
                <td><a href="tel:1800221911">1800 22 1911</a></td>
                <td><a href="mailto:complaints@centralbank.co.in">complaints@centralbank.co.in</a></td>
            </tr>
            <tr>
                <td>37</td>
                <td><b>CITIZEN CREDIT CO-OP BANK LTD.</b></td>
                <td>ST.XAVIERS SCHOOL COMPOUND,<br />
NEAR METRO THEATRE,<br />
DHOBITALAO, <br />
MUMBAI 400001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>38</td>
                <td><b>THE CLEARING CORPORATION OF INDIA LTD.</b></td>
                <td>F.P.No 822, Opp. Neat House, <br />
College lane, Off S K Bole Road,<br />
Dadar (W), Mumbai-400028</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>39</td>
                <td><b>JP MORGAN CHASE BANK</b></td>
                <td>JP Morgan Tower, <br />
Off CST Road, Kalina, <br />
Santacruz (E)<br />
Mumbai - 400098</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>40</td>
                <td><b>CITI BANK N.A.</b></td>
                <td>First International Financial Centre (FIFC)  <br />
G Block, Bandra Kurla Complex, <br />
Bandra East, Mumbai 400051</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>41</td>
                <td><b>CITY UNION BANK</b></td>
                <td>706, ANNA SALAI, <br />
CHENNAI 600 006</td>
                <td><a href="https://cityunionbank.bank.in/" target="_blank" class="text-primary font-weight-bold">cityunionbank.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>42</td>
                <td><b>CAPITAL SMALL FINANCE BANK LIMITED</b></td>
                <td>3RD, FLOOR, <br />
MIDAS CORPORATE PARK, <br />
37, G.T.ROAD, <br />
JALANDHAR</td>
                <td><a href="https://www.capital.bank.in" target="_blank" class="text-primary font-weight-bold">capital.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>43</td>
                <td><b>CANARA BANK</b></td>
                <td>112, JC ROAD  <br />
BANGALORE  <br />
KARNATAKA - 560002</td>
                <td><a href="https://www.canarabank.com" target="_blank" class="text-primary font-weight-bold">canarabank.com</a><br>
<a href="https://canarabank.bank.in" target="_blank" class="text-success small"><i class="fas fa-shield-alt"></i> canarabank.bank.in</a></td>
                <td><a href="tel:18004250018">1800 425 0018</a></td>
                <td><a href="mailto:hocancare@canarabank.com">hocancare@canarabank.com</a></td>
            </tr>
            <tr>
                <td>44</td>
                <td><b>COASTAL LOCAL AREA BANK LTD.</b></td>
                <td>D No. 59-14-2A, 3rd Floor, Santhi Plaza, Ring Road, Near Benz Circle, Vijayawada - 520008</td>
                <td><a href="https://coastal.bank.in" target="_blank" class="text-primary font-weight-bold">coastal.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>45</td>
                <td><b>THE COSMOS CO-OPERATIVE BANK LTD.</b></td>
                <td>Forex and Treasury Dept., <br />
Mumbai Regional Office<br />
36/A, Maru Niketan,<br />
D.L. Vaidya Road, Dadar (West),<br />
Mumbai 400028</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>46</td>
                <td><b>UBS AG</b></td>
                <td>10th floor, Ceejay house, <br />
Plot -F, Shiv Sagar Estate, <br />
Dr. Annie Besant Road, <br />
Worli, Mumbai - 400018</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>47</td>
                <td><b>CHHATTISGARH RAJYA GRAMIN BANK</b></td>
                <td>MAHADEOGHAT ROAD, SUNDER NAGAR, RAIPUR C. G - 492013</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>48</td>
                <td><b>CREDIT AGRICOLE CORPORATE AND INVESTMENT BANK</b></td>
                <td>Hoechst House,<br />
11th ,12th &amp; 14th Floor,<br />
Nariman Point,<br />
Mumbai 400021</td>
                <td><a href="https://www.ca-cib.com/en" target="_blank" class="text-primary font-weight-bold">ca-cib.com/en</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>49</td>
                <td><b>SHRI CHHATRAPATI RAJARSHI SHAHU URBAN CO-OP BANK</b></td>
                <td>Janadhar Bhavan&quot;  Jalna Road  Beed Maharashtra&quot;</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>50</td>
                <td><b>CSB BANK LIMITED</b></td>
                <td>Market Road, <br />
Ernakulam, <br />
Kerala -682011</td>
                <td><a href="https://www.csb.bank.in/" target="_blank" class="text-primary font-weight-bold">csb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>51</td>
                <td><b>CTBC Bank CO., Ltd.</b></td>
                <td>A1-16, Wenger House, <br />
Rajiv chowk, Connaught Place<br />
New Delhi -110001</td>
                <td><a href="https://www.ctbcbank.bank.in/content/dam/cbminisite/IN/index.html" target="_blank" class="text-primary font-weight-bold">ctbcbank.bank.in/content/da...</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>52</td>
                <td><b>DBS BANK INDIA LIMITED</b></td>
                <td>221 Dr D N Road, <br />
Fort, Mumbai -400001</td>
                <td><a href="https://www.dbs.bank.in/in/index/default.page" target="_blank" class="text-primary font-weight-bold">dbs.bank.in/in/index/defaul...</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>53</td>
                <td><b>DCB BANK LTD.</b></td>
                <td>Level &#039;6&#039;, Tower &#039;A&#039;,<br />
Peninsula Business Park,<br />
Senapati Bapat Marg,<br />
Lower Parel, Mumbai - 400013</td>
                <td><a href="https://www.dcb.bank.in/" target="_blank" class="text-primary font-weight-bold">dcb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>54</td>
                <td><b>The Darussaalam Co-operative Urban Bank Ltd.</b></td>
                <td>5-6-995, Darussalam Road,<br />
Aghapura,<br />
Hyderabad-500001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>55</td>
                <td><b>DEOGIRI NAGARI SAHAKARI BANK LTD AURANGABAD</b></td>
                <td>ARTH COMPLEX <br />
KESARSINGHPURA<br />
ADALAT ROAD<br />
AURANGABAD 431001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>56</td>
                <td><b>DEUTSCHE BANK AG</b></td>
                <td>Floor 3, Nirlon Knowledge Park, <br />
Block 1, Western Express Highway  <br />
Goregaon (East), Mumbai</td>
                <td><a href="https://www.deutsche.bank.in/" target="_blank" class="text-primary font-weight-bold">deutsche.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>57</td>
                <td><b>DEPOSIT INSURANCE AND CREDIT GUARANTEE CORPORATION</b></td>
                <td>Deptt. of Information Technology <br />
Reserve Bank of India Building, <br />
2nd Floor,<br />
Opp. Mumbai Central Railway Station,<br />
Mumbai Central, Mumbai - 400 008</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>58</td>
                <td><b>THE DELHI STATE COOPERATIVE BANK</b></td>
                <td>31, Netaji Subhash Marg, Darya Gnaj New Delhi- 110002</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>59</td>
                <td><b>DHANLAXMI BANK LTD.</b></td>
                <td>4th Floor, Corporate Office,<br />
Naicknnal Jn., Swaraj Round, <br />
Thrissur -1</td>
                <td><a href="https://www.dhan.bank.in/" target="_blank" class="text-primary font-weight-bold">dhan.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>60</td>
                <td><b>DMK JAOLI SAHAKARI BANK LTD.</b></td>
                <td>418/20, MAULANA AZAD ROAD, MUMBAI- 400 004</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>61</td>
                <td><b>DOMBIVLI NAGARI SAHAKARI BANK</b></td>
                <td>Madhukunj, P-52, <br />
M.I.D.C., Kalyan-Shil Road, <br />
Dombivli (East) - 421 204 <br />
Thane</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>62</td>
                <td><b>DOHA BANK</b></td>
                <td>Sakhar Bhavan, Ground Floor,<br />
Plot no.230, Block no. 3, Backbay Reclamation, Nariman Point, Mumbai - 400021</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>63</td>
                <td><b>DURGAPUR STEEL PEOPLES&#039; CO-OP BANK LTD</b></td>
                <td>BENACHITY HOUSE, DURGAPUR,<br />
DIST-BARDHAMAN,<br />
WEST BENGAL,<br />
713204.</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>64</td>
                <td><b>EMIRATES NBD BANK PJSC</b></td>
                <td>G-1, GROUND FLOOR &amp; 11, 1ST FLOOR, 5 NORTH AVENUE,<br />
MAKER MAXITY, BANDRA KURLA LINK ROAD,<br />
BKC, MUMBAI 400051</td>
                <td><a href="https://www.emiratesnbd.bank.in/en" target="_blank" class="text-primary font-weight-bold">emiratesnbd.bank.in/en</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>65</td>
                <td><b>EXPORT IMPORT BANK OF INDIA</b></td>
                <td>CENTRE ONE BUILDING, FLOOR 21, WORLD TRADE CENTRE COMPLEX, CUFFE PARADE, MUMBAI-400005</td>
                <td><a href="https://www.eximbankindia.in/" target="_blank" class="text-primary font-weight-bold">eximbankindia.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>66</td>
                <td><b>EQUITAS SMALL FINANCE BANK LTD.</b></td>
                <td>4th Floor, Phase II, Spencer Plaza, No. 769, Anna Salai, Chennai, Tamil Nadu-600002</td>
                <td><a href="https://equitas.bank.in/" target="_blank" class="text-primary font-weight-bold">equitas.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>67</td>
                <td><b>ESAF SMALL FINANCE BANK LTD.</b></td>
                <td>ESAF small finance bank ltd., Hepzibah Complex, Second Floor, No.X/109/M4, Mannuthy P.O., Thrissur, Kerala, India 680651</td>
                <td><a href="https://www.esaf.bank.in/" target="_blank" class="text-primary font-weight-bold">esaf.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>68</td>
                <td><b>FEDERAL BANK LTD.</b></td>
                <td>Federal Heights, <br />
Fed Tech Center, <br />
Alwaye, Kerala-683101</td>
                <td><a href="https://www.federal.bank.in/" target="_blank" class="text-primary font-weight-bold">federal.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>69</td>
                <td><b>FINO PAYMENTS BANK LIMITED</b></td>
                <td>SHREE SAWAN KNOWLEDGE PARK, PLOT NO. D-507, 2nd FLOOR, MIDC TURBHE, NAVI MUMBAI, MAHARASHTRA - 400 705</td>
                <td><a href="https://fino.bank.in/" target="_blank" class="text-primary font-weight-bold">fino.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>70</td>
                <td><b>FIRST RAND BANK LTD</b></td>
                <td>4th floor, TCG Financial Centre, <br />
C-53 G&quot; Block  Bandra-Kurla complex Bandra(E) Mumbai - 400 051&quot;</td>
                <td><a href="https://www.firstrand.co.in/" target="_blank" class="text-primary font-weight-bold">firstrand.co.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>71</td>
                <td><b>The Gayatri Co-operative Urban Bank Ltd</b></td>
                <td>#1-5-171/A, Gayatri Tower-II, Karimnagar road,Jagtial,Telangana-505327</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>72</td>
                <td><b>THE GREATER BOMBAY CO-OPERATIVE BANK LTD.</b></td>
                <td>GBCB House, 89, <br />
Bhuleshwar, <br />
Mumbai - 02.</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>73</td>
                <td><b>THE GADCHIROLI DISTRICT CENTRAL CO-OP BANK LTD.</b></td>
                <td>Near Bus Stop, Dhanora Road<br />
PO Gadchiroli,<br />
Dist. Gadchiroli -442605<br />
Maharashtra</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>74</td>
                <td><b>THE GUJARAT STATE CO-OPERATIVE BANK LTD</b></td>
                <td>Sahakar Bhavan <br />
P.O. Box #  302, <br />
Tilak Road, <br />
Ahmedabad - 380001  <br />
Gujarat</td>
                <td><a href="http://www.ggb.bank.in/" target="_blank" class="text-primary font-weight-bold">ggb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>75</td>
                <td><b>GOLDMAN SACHS INDIA CAPITAL MARKETS PVT LTD.</b></td>
                <td>951-A, RATIONAL HOUSE, <br />
APPASAHED MARATHE MARG,<br />
PRABHADEVI,<br />
MUMBAI - 400 025.</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>76</td>
                <td><b>GUARDIAN SOURHARDA SAHAKARI BANK NIYAMITA</b></td>
                <td>NO 139 GUARDIAN HOUSE INFANTRY ROAD SHIVAJINAGAR BANGALORE 560 001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>77</td>
                <td><b>THE HARYANA STATE COOPERATIVE APEX BANK LIMITED</b></td>
                <td>SCO 78-80, BANK SQUARE, SECTOR-17 B, CHANDIGARH</td>
                <td><a href="http://www.shgb.bank.in/" target="_blank" class="text-primary font-weight-bold">shgb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>78</td>
                <td><b>THE HASTI CO-OP BANK LTD.</b></td>
                <td>HASTI SAHAKAR DEEP STATION<br />
AREA DONDAICHA<br />
HO. A/P DONDAICHA DIST. DHULE<br />
PIN:425408</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>79</td>
                <td><b>HDFC BANK LTD.</b></td>
                <td>4th floor, HDFC Bank House, <br />
Senapati Bapat Marg, <br />
Opp. Kamala Mills, <br />
Lower Parel,  Mumbai -13.</td>
                <td><a href="https://www.hdfcbank.com" target="_blank" class="text-primary font-weight-bold">hdfcbank.com</a><br>
<a href="https://hdfcbank.bank.in" target="_blank" class="text-success small"><i class="fas fa-shield-alt"></i> hdfcbank.bank.in</a></td>
                <td><a href="tel:18002026161">1800 202 6161</a><br><a href="tel:18602676161">1860 267 6161</a></td>
                <td><a href="mailto:support@hdfcbank.com">support@hdfcbank.com</a></td>
            </tr>
            <tr>
                <td>80</td>
                <td><b>HITACHI PAYMENT SERVICES PVT. LTD.</b></td>
                <td>401, 4th Floor, Silver metropolis, Jai Coach Compound, Off. Western Express highway, Goregaon East, Mumbai - 400063</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>81</td>
                <td><b>THE HIMACHAL PRADESH STATE CO-OPERATIVE BANK LTD.</b></td>
                <td>THE HIMACHAL PRADESH STATE CO-OPERATIVE BANK LTD., BANK BUILDING NO 1, HEAD OFFICE, THE MALL, SHIMLA-171001</td>
                <td><a href="http://www.hpgb.bank.in/" target="_blank" class="text-primary font-weight-bold">hpgb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>82</td>
                <td><b>HSBC BANK</b></td>
                <td>52/60, MG ROAD, <br />
Fort, Mumbai-400001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>83</td>
                <td><b>Hutatma Sahakari Bank Ltd</b></td>
                <td>B TYPE KAMGAR BHAVAN KARKHANA ROAD WALWA   SANGLI - 416313</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>84</td>
                <td><b>WOORI BANK</b></td>
                <td>Chennai Branch, 6th Floor, <br />
EA Chambers, No:49, 50L, <br />
Whites Road, Royapettah, <br />
Chennai - 600014</td>
                <td><a href="https://go.wooribank.com/in/ib/main/IbMain.do" target="_blank" class="text-primary font-weight-bold">go.wooribank.com/in/ib/main...</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>85</td>
                <td><b>PT BANK MAYBANK INDONESIA TBK</b></td>
                <td>One Indiabulls centre, <br />
Tower 2B, 7 floor, <br />
Elphinstone Road, <br />
Mumbai-400013</td>
                <td><a href="https://www.maybank.co.in/" target="_blank" class="text-primary font-weight-bold">maybank.co.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>86</td>
                <td><b>IDBI BANK LTD.</b></td>
                <td>IDBI Bank, IDBI Tower, <br />
Cuffe Parade, <br />
Mumbai - 400 005.</td>
                <td><a href="https://www.idbibank.in" target="_blank" class="text-primary font-weight-bold">idbibank.in</a><br>
<a href="https://idbi.bank.in" target="_blank" class="text-success small"><i class="fas fa-shield-alt"></i> idbi.bank.in</a></td>
                <td><a href="tel:18002094324">1800 209 4324</a><br><a href="tel:1800221070">1800 22 1070</a></td>
                <td><a href="mailto:customercare@idbi.co.in">customercare@idbi.co.in</a></td>
            </tr>
            <tr>
                <td>87</td>
                <td><b>INDUSTRIAL BANK OF KOREA</b></td>
                <td>INDUSTRIAL BANK OF KOREA<br />
OFFICE NO. 403<br />
4th FLOOR, WORLDMARK 2 (ASSET 8)<br />
HOSPITALITY DISTRICT, AEROCITY<br />
NEW DELHI</td>
                <td><a href="https://in.globalibk.com/iview/03/CMIBMAN0000" target="_blank" class="text-primary font-weight-bold">in.globalibk.com/iview/03/C...</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>88</td>
                <td><b>INDUSTRIAL AND COMMERCIAL BANK OF CHINA LTD.</b></td>
                <td>801, 8th Floor, A- Wing,<br />
ONE BKC, C-66, G Block,<br />
Bandra Kurla Complex,<br />
Bandra(E)<br />
Mumbai-400051</td>
                <td><a href="http://www.icbcindia.com/" target="_blank" class="text-primary font-weight-bold">icbcindia.com</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>89</td>
                <td><b>ICICI BANK LTD.</b></td>
                <td>ICICI Bank Towers, <br />
Bandra-Kurla Complex, <br />
Mumbai 400 051.</td>
                <td><a href="https://www.icicibank.com" target="_blank" class="text-primary font-weight-bold">icicibank.com</a><br>
<a href="https://icicibank.bank.in" target="_blank" class="text-success small"><i class="fas fa-shield-alt"></i> icicibank.bank.in</a></td>
                <td><a href="tel:18001080">1800 1080</a></td>
                <td><a href="mailto:customer.care@icicibank.com">customer.care@icicibank.com</a></td>
            </tr>
            <tr>
                <td>90</td>
                <td><b>INDIAN CLEARING CORPORATION LTD.</b></td>
                <td>FLOOR 25,<br />
P J TOWERS,<br />
DALAL STREET,<br />
MUMBAI</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>91</td>
                <td><b>IDFC FIRST Bank Limited</b></td>
                <td>KRM Tower, 8th Floor, No.1,<br />
Harrington Road,<br />
Chetpet,<br />
Chennai-600031</td>
                <td><a href="https://www.idfcfirst.bank.in/" target="_blank" class="text-primary font-weight-bold">idfcfirst.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>92</td>
                <td><b>INDIAN BANK</b></td>
                <td>CORPORATE OFFICE, <br />
254-260 AVVAI SHANMUGAM SALAI,<br />
ROYAPETTAH, CHENNAI 600014</td>
                <td><a href="https://www.indianbank.in" target="_blank" class="text-primary font-weight-bold">indianbank.in</a><br>
<a href="https://indianbank.bank.in" target="_blank" class="text-success small"><i class="fas fa-shield-alt"></i> indianbank.bank.in</a></td>
                <td><a href="tel:180042500000">1800 425 00000</a></td>
                <td><a href="mailto:customercomplaints@indianbank.co.in">customercomplaints@indianbank.co.in</a></td>
            </tr>
            <tr>
                <td>93</td>
                <td><b>INDUSIND BANK LTD.</b></td>
                <td>8th Flr, Tower1,<br />
One India Bull Center, <br />
841, Senapati Bapat Marg,<br />
Elphinstone Road, <br />
Mumbai - 400013</td>
                <td><a href="https://www.indusind.com" target="_blank" class="text-primary font-weight-bold">indusind.com</a><br>
<a href="https://indusind.bank.in" target="_blank" class="text-success small"><i class="fas fa-shield-alt"></i> indusind.bank.in</a></td>
                <td><a href="tel:18602677777">1860 267 7777</a></td>
                <td><a href="mailto:reachus@indusind.com">reachus@indusind.com</a></td>
            </tr>
            <tr>
                <td>94</td>
                <td><b>INDIA1 PAYMENTS LIMITED</b></td>
                <td>Unit no. 801 to 810, 8th floor, Tower-B<br />
Diamond District,  #150,<br />
Old airport road, Bengaluru-560008</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>95</td>
                <td><b>INDIAN OVERSEAS BANK</b></td>
                <td>763, ANNASALAI, <br />
CHENNAI - 600002</td>
                <td><a href="https://www.iob.bank.in/" target="_blank" class="text-primary font-weight-bold">iob.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>96</td>
                <td><b>INDIA POST PAYMENTS BANK LIMITED</b></td>
                <td>Post Office, Speed Post Centre Building, Market Road, New Delhi, Central Delhi, Delhi - 110001</td>
                <td><a href="https://ippbonline.bank.in/" target="_blank" class="text-primary font-weight-bold">ippbonline.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>97</td>
                <td><b>ICICI SECURITIES PRIMARY DEALERSHIP LTD.</b></td>
                <td>ICICI Centre, <br />
H.T. Parekh Marg,<br />
Churchgate, <br />
Mumbai 400020</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>98</td>
                <td><b>IRINJALAKUDA TOWN COOPERATIVE BANK LTD</b></td>
                <td>Tana South, Irinjalakuda, Thrissur Dist, Kerala - 680121</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>99</td>
                <td><b>THE JAMMU AND KASHMIR BANK LTD.</b></td>
                <td>20/4, 5th Floor, <br />
Palm Court, Sukhrali Chowk, <br />
Gurgoan, Haryana -122001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>100</td>
                <td><b>JANASEVA SAHAKARI BANK LTD., PUNE</b></td>
                <td>156, Gandhi Chowk, <br />
Madapsar, <br />
Pune-28</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>101</td>
                <td><b>JANASEVA SAHAKARI BANK (BORIVLI) LTD.</b></td>
                <td>J C POOJARY,<br />
DR D G PALKAR MARG,<br />
BORIVLI WEST,<br />
MUMBAI</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>102</td>
                <td><b>JIO PAYMENTS BANK LIMITED</b></td>
                <td>3rd FLOOR, MAKER CHAMBERS IV, <br />
222, NARIMAN POINT, MUMBAI-400021</td>
                <td><a href="https://www.jiopayments.bank.in/" target="_blank" class="text-primary font-weight-bold">jiopayments.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>103</td>
                <td><b>JALGAON JANATA SAHAKARI BANK LTD.</b></td>
                <td>Sewa, 117/119 <br />
Navi Peth, Jalgaon<br />
Maharashtra</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>104</td>
                <td><b>THE JALGAON PEOPLE CO-OPERATIVE BANK LTD.</b></td>
                <td>152, Polan Peth,<br />
Dana Bazar,<br />
Jalgaon -425001<br />
Maharashtra</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>105</td>
                <td><b>JANKALYAN SAHAKARI BANK LTD.</b></td>
                <td>Vivek Darshan, 140 Sindhi Society, <br />
Chembur, Mumbai-400071</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>106</td>
                <td><b>JANATA SAHAKARI BANK LTD., PUNE</b></td>
                <td>1360, Shukrawar Peth,<br />
Pune, Maharashtra</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>107</td>
                <td><b>JANA SMALL FINANCE BANK LIMITED</b></td>
                <td>&#039;VAISHNAVI&#039;, NO.29, UNION STREET, OFF INFANTRY ROAD, BENGALURU - 560 001</td>
                <td><a href="https://www.jana.bank.in/" target="_blank" class="text-primary font-weight-bold">jana.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>108</td>
                <td><b>JANATHA SEVA COOPERATIVE BANK LTD</b></td>
                <td>No. 14, Rajatha bhavana, <br />
1st Main Road, Hampinagara,<br />
Vijayanagar 2nd Stage, Bangalore<br />
560104</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>109</td>
                <td><b>THE KANGRA CENTRAL CO-OP BANK LTD., DHARAMSHALA</b></td>
                <td>SEHKAR JYOTI BUILDING, <br />
CIVIL LINES, <br />
DHARAMSHALA (H.P.) - 176 215</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>110</td>
                <td><b>KALLAPPANNA AWADE ICHALKARANJI JANATA SAH BANK LTD</b></td>
                <td>Ward No. 12, House No. 1, <br />
Main Road, &#039;Janata Bank Bhavan&#039;,<br />
Ichalkaranji - 416115,<br />
Kolhapur, Maharashtra</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>111</td>
                <td><b>THE KANGRA CO-OPERATIVE BANK LTD.</b></td>
                <td>C-29, Community Centre, <br />
Pankha Road , Near Janak Cinema, <br />
Janakpuri, <br />
New Delhi -110058</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>112</td>
                <td><b>KARNATAKA BANK LTD</b></td>
                <td>Head Office,<br />
Mahaveer Circle Kankanady,<br />
Mangalore - 575002, <br />
Dist: Dakshina Kannada, <br />
Karnataka</td>
                <td><a href="http://www.karnatakagb.bank.in/" target="_blank" class="text-primary font-weight-bold">karnatakagb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>113</td>
                <td><b>KOOKMIN BANK</b></td>
                <td>UNIT NO.2B/2, 2ND FLOOR, TWO HORIZON CENTRE, GOLF COURSE ROAD, DLF PHASE5, SECTOR-43,GURUGRAM 12202, HARYANA</td>
                <td><a href="https://kbstar.bank.in/ind/main" target="_blank" class="text-primary font-weight-bold">kbstar.bank.in/ind/main</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>114</td>
                <td><b>THE KALUPUR COMMERCIAL CO-OPERATIVE BANK LTD.</b></td>
                <td>Kalupur Bank Bhavan,<br />
Near Income Tax Circle,<br />
Ashram Road,<br />
Ahmedabad - 380014</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>115</td>
                <td><b>THE KALYAN JANATA SAHAKARI BANK LTD.</b></td>
                <td>Janata Bhavan,<br />
Bhartacharya vaidya chowk<br />
Behind Telwane Hospital<br />
Kalyan (w) - 421 301.</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>116</td>
                <td><b>KOTAK MAHINDRA BANK LTD.</b></td>
                <td>Kotak Infiniti, <br />
1st Floor, Building No. 21, <br />
Infinity Park, <br />
Western Express Highway,<br />
Gen AK Vaidya Marg,<br />
Malad (E),Mumbai-400097</td>
                <td><a href="https://www.kotak.bank.in/en/home.html" target="_blank" class="text-primary font-weight-bold">kotak.bank.in/en/home.html</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>117</td>
                <td><b>KERALA GRAMIN BANK</b></td>
                <td>PB NO. 10, HEAD OFFICE,KGB TOWERS, AK ROAD,MALAPPURAM, KERALA, PIN- 676505</td>
                <td><a href="http://www.kgb.bank.in/" target="_blank" class="text-primary font-weight-bold">kgb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>118</td>
                <td><b>The Kurla Nagarik Sahakari Bank Ltd</b></td>
                <td>312,Commercial A Wing,Kohinoor City Mall,Kirol Road,Off LBS Marg,Kurla(West),Mumbai,Maharashtra-400070</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>119</td>
                <td><b>KURMANCHAL NAGAR SAHAKARI BANK LTD.</b></td>
                <td>KURMANCHAL BHAWAN,<br />
TALLITAL,NAINITAL- 263 002<br />
UTTARAKHAND</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>120</td>
                <td><b>KEB HANA BANK</b></td>
                <td>4th Floor, Bannari Amman Towers,<br />
No.29, Dr. Radhakrishnan Road,<br />
Mylapore, Chennai- 600004</td>
                <td><a href="https://global.1qbank.com/lounge/chennai/et/main.html" target="_blank" class="text-primary font-weight-bold">global.1qbank.com/lounge/ch...</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>121</td>
                <td><b>KOLHAPUR URBAN COOPERATIVE BANK LTD</b></td>
                <td>514 D WARD, GANGAWESH, KOLHAPUR- 416002</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>122</td>
                <td><b>THE KERALA STATE CO-OPERATIVE BANK LTD.</b></td>
                <td>P.B. NO. 6515<br />
COBANK TOWERS<br />
PALAYAM<br />
THIRUVANANTHAPURAM<br />
PIN - 695 033</td>
                <td><a href="http://www.kgb.bank.in/" target="_blank" class="text-primary font-weight-bold">kgb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>123</td>
                <td><b>KARNATAKA STATE CO-OPERATIVE APEX BANK LTD.</b></td>
                <td>NO.1, UTHUNGA, <br />
PAMPAMAHAKAVI ROAD, <br />
CHAMARJPET, <br />
BANGALORE-560018</td>
                <td><a href="http://www.karnatakagb.bank.in/" target="_blank" class="text-primary font-weight-bold">karnatakagb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>124</td>
                <td><b>THE KARAD URBAN CO-OPERATIVE BANK LTD., KARAD</b></td>
                <td>Head Office, <br />
516/2 Shahu Chowk, <br />
Shaniwar Peth <br />
Karad - 415110<br />
Maharashtra</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>125</td>
                <td><b>KARUR VYSYA BANK</b></td>
                <td>Central Office, Post Box No. 21, <br />
Erode Road, Karur - 639002<br />
Tamil Nadu</td>
                <td><a href="https://www.kvb.bank.in" target="_blank" class="text-primary font-weight-bold">kvb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>126</td>
                <td><b>THE LATUR URBAN CO-OPERATIVE BANK LTD</b></td>
                <td>Late Ramgopalji Rathi Business Centre, Shivaji Nagar, Latur 413512</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>127</td>
                <td><b>BANK OF MAHARASHTRA</b></td>
                <td>1501, SHIVAJINAGAR, <br />
PUNE - 411005<br />
MAHARASHTRA</td>
                <td><a href="https://www.mahagramin.bank.in/" target="_blank" class="text-primary font-weight-bold">mahagramin.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>128</td>
                <td><b>MAHARASHTRA GRAMIN BANK</b></td>
                <td>MAHARASHTRA GRAMIN BANK, HEAD OFFICE, PLOT NO. 25, JIVANSHREE, TOWN CENTRE, CIDCO, AURANGABAD-431005<br />
MAHARASHTRA</td>
                <td><a href="https://www.mahagramin.bank.in/" target="_blank" class="text-primary font-weight-bold">mahagramin.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>129</td>
                <td><b>THE MEGHALAYA CO-OPERATIVE APEX BANK LIMITED</b></td>
                <td>MAHATMA GANDHI ROAD, SHILLONG, MEGHALAYA - 793001</td>
                <td><a href="http://www.meghalayaruralbank.bank.in/" target="_blank" class="text-primary font-weight-bold">meghalayaruralbank.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>130</td>
                <td><b>GS MAHANAGAR CO-OPERATIVE BANK LIMITED, MUMBAI</b></td>
                <td>Maze maher bldg.hall,<br />
Parel tank Road,<br />
Abhudayanagar,<br />
Mumbai-400 033</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>131</td>
                <td><b>MODEL CO-OP. BANK LTD.</b></td>
                <td>14-B, VATSA HOUSE, JANMABHOOMI MARG, FORT, MUMBAI 400023</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>132</td>
                <td><b>MUMBAI DISTRICT CENTRAL CO-OPERATIVE BANK LTD.</b></td>
                <td>207, D. N. ROAD, <br />
FORT, <br />
MUMBAI 400 001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>133</td>
                <td><b>MIZUHO BANK LTD.</b></td>
                <td>Maker Chambers III, First floor, <br />
Jamnalal Bajaj Road, <br />
Nariman Point<br />
Mumbai- 400 021</td>
                <td><a href="https://www.mizuhogroup.com/bank" target="_blank" class="text-primary font-weight-bold">mizuhogroup.com/bank</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>134</td>
                <td><b>MAHESH SAHAKARI BANK LIMITED, PUNE</b></td>
                <td>Plot No. 372/73/74, <br />
Market Yard,<br />
Gultekadi,<br />
Pune - 411037</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>135</td>
                <td><b>THE MAHARASHTRA STATE CO-OPERATIVE BANK LTD.</b></td>
                <td>9, Maharashtra Chamber of Commerce Lane, <br />
Fort, Mumbai 400001</td>
                <td><a href="https://www.mahagramin.bank.in/" target="_blank" class="text-primary font-weight-bold">mahagramin.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>136</td>
                <td><b>MASHREQBANK PSC</b></td>
                <td>1305, RAHEJA CENTRE, <br />
NARIMAN POINT, <br />
MUMBAI - 400021</td>
                <td><a href="https://mashreq.bank.in/en/corporate/" target="_blank" class="text-primary font-weight-bold">mashreq.bank.in/en/corporate</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>137</td>
                <td><b>THE MUSLIM CO-OPERATIVE BANK LTD., PUNE</b></td>
                <td>633, RAVIWAR PETH, SAIFEE MAHAL, PUNE - 411002</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>138</td>
                <td><b>MEHSANA URBAN CO-OPERATIVE BANK LTD.</b></td>
                <td>Urban Bank Road, <br />
Highway, Mehsana - 384002, <br />
Gujarat</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>139</td>
                <td><b>MORGAN STANLEY INDIA PRIMARY DEALER PVT LTD.</b></td>
                <td>18F/19F, Tower 2, One Indiabulls Centre | 841, Senapati Bapat Marg</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>140</td>
                <td><b>MASTERCARD ASIA PACIFIC PTE. LIMITED</b></td>
                <td>3 Fraser Street,<br />
#17-21128<br />
Duo Tower, Singapore (189352)</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>141</td>
                <td><b>THE MUNICIPAL CO-OPERATIVE BANK LTD., MUMBAI</b></td>
                <td>Municipal Bank Bhavan, <br />
245, P&#039;Demello Rd, <br />
Fort, Mumbai -400001.</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>142</td>
                <td><b>SIR M. VISVESVARAYA CO-OPERATIVE BANK LTD.</b></td>
                <td>#109, SHANKARAMUTT ROAD, SHANKARAPURAM, BANGALORE - 560004</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>143</td>
                <td><b>FIRST ABU DHABI BANK PJSC</b></td>
                <td>UNIT 1101 AND 1201, PLATINA BUILDING, C-59, G-BLOCK, BANDRA KURLA COMPLEX, MUMBAI- 400 051</td>
                <td><a href="https://www.bankfab.com/en-in" target="_blank" class="text-primary font-weight-bold">bankfab.com/en-in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>144</td>
                <td><b>NABARD</b></td>
                <td>PLOT NO. C-24, &#039;G&#039; BLOCK,<br />
BANDRA-KURLA COMPLEX,<br />
BANDRA (E),<br />
MUMBAI - 400 051</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>145</td>
                <td><b>NILAMBUR CO-OPERATIVE URBAN BANK</b></td>
                <td>NILAMBUR<br />
MALAPPURAM DISTRICT<br />
KERALA - 679329</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>146</td>
                <td><b>The Nasik Road Deolali Vyapari Sahakari  Bank  Ltd</b></td>
                <td>16, Kalpavruksh, Asha Nagar Nasik Road Nashik-422101</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>147</td>
                <td><b>SLICE SMALL FINANCE BANK LIMITED</b></td>
                <td>1st &amp; 3rd Floor, Fortune Central, Basistha Road, Basisthapur, Bylane No 3, Beltola, Guwahati - 781028</td>
                <td><a href="https://slice.bank.in/" target="_blank" class="text-primary font-weight-bold">slice.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>148</td>
                <td><b>NOMURA FIXED INCOME SECURITIES PVT LTD.</b></td>
                <td>Ceejay House, Level 11, <br />
Shivsagar Estate, <br />
Dr. Annie Besant Road, <br />
Worli, Mumbai</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>149</td>
                <td><b>NAGPUR NAGRIK SAHAKARI BANK LTD.</b></td>
                <td>Dr.Ambedkar Chowk,<br />
Central Avenue Road,<br />
Nagpur-440008</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>150</td>
                <td><b>NONGHYUP BANK</b></td>
                <td>Unit 108, 1st Floor, Tower C<br />
Advant Navis Business Tower,<br />
Plot No. 7, Noida - Greater Noida Expy,<br />
Sector 142, Gautam Buddha Nagar,<br />
Noida, Uttar Pradesh - 201305</td>
                <td><a href="https://nonghyup.bank.in/index.jsp" target="_blank" class="text-primary font-weight-bold">nonghyup.bank.in/index.jsp</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>151</td>
                <td><b>THE NAV JEEVAN CO-OP. BANK LTD.</b></td>
                <td>BHAWANI SAW MILLS COMPOUND<br />
ULHASNAGAR, 421 003</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>152</td>
                <td><b>NKGSB CO-OPERATIVE BANK LTD.</b></td>
                <td>LAXMI SADAN, <br />
361, V.P.ROAD,<br />
GIRGAON, <br />
MUMBAI  400 004.</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>153</td>
                <td><b>NASIK MERCHANTS CO-OPERATIVE BANK LTD.</b></td>
                <td>A16, MIODC, <br />
Satpur, <br />
Nasik-422007</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>154</td>
                <td><b>NUTAN NAGARIK SAHAKARI BANK LTD.</b></td>
                <td>Opp. Samartheshwar Mahadev,<br />
Nr. Law Garden, Ellisbridge,<br />
Ahmedabad - 380006<br />
Gujarat</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>155</td>
                <td><b>THE BANK OF NOVA SCOTIA</b></td>
                <td>B-wing, Mittal Tower, <br />
Nariman Point,<br />
Mumbai</td>
                <td><a href="http://www.scotiabank.com/global/en/0,,6176,00.html" target="_blank" class="text-primary font-weight-bold">scotiabank.com/global/en/0,...</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>156</td>
                <td><b>NATIONAL PAYMENTS CORPORATION OF INDIA</b></td>
                <td>C-9, 8th Floar, <br />
RBI premises, <br />
BKC, Bandra(E),<br />
Mumbai - 400051</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>157</td>
                <td><b>NSDL Payments Bank Limited</b></td>
                <td>4th Floor, &#039;A&#039; Wing, Trade World<br />
Kamala Mills Compound<br />
Senapati Bapat Marg<br />
Lower Parel<br />
Mumbai - 400013</td>
                <td><a href="https://nsdlpayments.bank.in/" target="_blank" class="text-primary font-weight-bold">nsdlpayments.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>158</td>
                <td><b>THE NAINITAL BANK LTD.</b></td>
                <td>7 OAKS MALL ROAD, <br />
MALLITAL, <br />
NAINITAL</td>
                <td><a href="https://www.nainitalbank.bank.in/english/home.aspx" target="_blank" class="text-primary font-weight-bold">nainitalbank.bank.in/englis...</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>159</td>
                <td><b>THE NAVNIRMAN CO-OPERATIVE BANK LIMITED</b></td>
                <td>THE NAVNIRMAN CO-OPERATIVE BANK LTD., NAVNIRMAN BANK BHAVAN, OPP. SHRIMALI SOCIETY, RASALA MARG, NAVRANGPURA, AHMEDABAD-380009</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>160</td>
                <td><b>ODISHA STATE CO-OPERATIVE BANK LTD.</b></td>
                <td>PT. JAWAHARLAL NEHRU MARG, <br />
BHUBANESWAR-751001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>161</td>
                <td><b>PhonePe Private Limited</b></td>
                <td>Office - 2, Floor 5,<br />
Wing A, Block A,<br />
Salarpuria Softzone, Bellandur Village<br />
Varthur Hobli, Outer Ring Road, Bellandur, Bangalore, Karnataka - 560103</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>162</td>
                <td><b>GP PARSIK SAHAKARI BANK LTD., KALWA, THANE</b></td>
                <td>SahakarMurti Gopinath Shivram Patil Bhavan, <br />
Parsik Nagar, Kalwa<br />
Thane - 400 605</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>163</td>
                <td><b>KARNATAKA GRAMEENA BANK</b></td>
                <td>HEAD OFFICE 32, SANGANKAL ROAD, BALLARI - 583103</td>
                <td><a href="http://www.karnatakagb.bank.in/" target="_blank" class="text-primary font-weight-bold">karnatakagb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>164</td>
                <td><b>PRIME CO-OPERATIVE BANK LTD</b></td>
                <td>3rd floor,  Tapi Villa  <br />
34, Vijay Nagar, <br />
3rd Gate, Majura Gate, <br />
Surat - 395 001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>165</td>
                <td><b>PNB GILTS LTD.</b></td>
                <td>PNB HOUSE,<br />
SIR PM ROAD,<br />
FORT, MUMBAI - 400 001.</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>166</td>
                <td><b>PAY POINT INDIA NETWORK PVT LTD</b></td>
                <td>A-203, SUPREME BUSINESS PARK, HIRANANDANI GARDEN, POWAI,MUMBAI 400076</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>167</td>
                <td><b>THE PAVANA SHAKARI BANK LTD</b></td>
                <td>H-BLOCK, PLOT NO-C.20, PIMPRI INDUSTRIAL AREA,NEAR AUTO CLUSTER, CHINCHWAD, PUNE - 411019</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>168</td>
                <td><b>PUNJAB AND SIND BANK</b></td>
                <td>BANK HOUSE, <br />
21, RAJENDRA PLACE, <br />
NEW DELHI-110008</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>169</td>
                <td><b>THE PANDHARPUR URBAN COOP BANK LTD</b></td>
                <td>4163 B, NAVI PETH, PANDHARPUR DIST SOLAPUR 413304, P.B. NO-18</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>170</td>
                <td><b>PUNJAB NATIONAL BANK</b></td>
                <td>7, Bhikaji Cama Place, <br />
New Delhi - 110607.</td>
                <td><a href="https://www.pnbindia.in" target="_blank" class="text-primary font-weight-bold">pnbindia.in</a><br>
<a href="https://pnb.bank.in" target="_blank" class="text-success small"><i class="fas fa-shield-alt"></i> pnb.bank.in</a></td>
                <td><a href="tel:18001802222">1800 180 2222</a><br><a href="tel:18001032222">1800 103 2222</a></td>
                <td><a href="mailto:care@pnb.co.in">care@pnb.co.in</a></td>
            </tr>
            <tr>
                <td>171</td>
                <td><b>THE PUSAD URBAN CO-OP BANK LTD</b></td>
                <td>TALAO LAYOUT, PUSAD, DIST- YAVATMAL, MAHARASHTRA. 445204</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>172</td>
                <td><b>QATAR NATIONAL BANK(Q.P.S.C)</b></td>
                <td>G-1A 4 NORTH AVENUE, MAKER MAXITY, BANDRA KURLA COMPLEX, BANDRA EAST, MUMBAI - 400051</td>
                <td><a href="https://qnb.bank.in/sites/qnb/qnbindia/page/en/en-home.html" target="_blank" class="text-primary font-weight-bold">qnb.bank.in/sites/qnb/qnbin...</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>173</td>
                <td><b>RBL BANK LTD.</b></td>
                <td>One India Bulls Centre,<br />
Tower 2 ,6th floor<br />
841, Senapati Bapat marg,<br />
Lower Parel(W), Mumbai</td>
                <td><a href="https://www.rbl.bank.in/" target="_blank" class="text-primary font-weight-bold">rbl.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>174</td>
                <td><b>RESERVE BANK OF INDIA</b></td>
                <td>Deposit Accounts Department,<br />
2nd floor, Main Buliding, <br />
Fort, Mumbai - 400 001.</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>175</td>
                <td><b>Jila Sahakari Kendriya Bank Maryadit, Rajnandgaon</b></td>
                <td>G. E. Road,<br />
Near Law College<br />
Rajnandgaon (C.G.)<br />
491441</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>176</td>
                <td><b>RAJASTHAN MARUDHARA GRAMIN BANK</b></td>
                <td>TULSI TOWER, 9TH B ROAD, SARDARPURA, JODHPUR (RAJ.) 342003</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>177</td>
                <td><b>RAJKOT NAGRIK SAHAKARI BANK LTD.</b></td>
                <td>Nagrik Bhavan -1, <br />
Dhebar Bhai Road,<br />
Para Bazar,<br />
Rajkot - 360001<br />
Gujarat</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>178</td>
                <td><b>RAJARAMBAPU SAHAKARI BANK LTD</b></td>
                <td>A/p Peth, Tal-Walwa, Dist.-Sangli, PIN-415407</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>179</td>
                <td><b>RAJGURUNAGAR SAHKARI BANK LTD.</b></td>
                <td>S No. 319/320, Pune Nashik Highway, Rajgurunagar- 410505<br />
Tal. Khed Dist. Pune</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>180</td>
                <td><b>THE RAJASTHAN STATE CO-OPERATIVE BANK LTD.</b></td>
                <td>DC-1, Opposite Nehru Balodyan, <br />
Tonk Road, <br />
Jaipur 302015</td>
                <td><a href="http://www.rgb.bank.in/" target="_blank" class="text-primary font-weight-bold">rgb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>181</td>
                <td><b>RAJARSHI SHAHU SAHAKARI BANK LTD, PUNE</b></td>
                <td>597, Shukrawar Peth, Shahu Complex, Shivaji Road, Pune  411 002</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>182</td>
                <td><b>SBER BANK</b></td>
                <td>UGF, Gopal Das Bhawan, <br />
28 Barakhamba Road, <br />
New Delhi-110085</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>183</td>
                <td><b>SANT SOPANKAKA SAHAKARI BANK LIMITED, SASWAD</b></td>
                <td>Sopankaka Bhavan,<br />
1039, Shukrawar Peth,<br />
Tilak Road,<br />
Pune - 411002</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>184</td>
                <td><b>SBI DFHI LTD.</b></td>
                <td>SBI DFHI LTD<br />
3rd Floor, Voltas House, <br />
J. N. Heredia Marg,<br />
Mumbai - 400001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>185</td>
                <td><b>STATE BANK OF INDIA</b></td>
                <td>IT-PSG, A-WING,<br />
FIRST FLOOR,<br />
STATE BANK GITC, <br />
CBD BELAPUR, <br />
NAVI MUMBAI</td>
                <td><a href="https://www.sbi.co.in" target="_blank" class="text-primary font-weight-bold">sbi.co.in</a><br>
<a href="https://sbi.bank.in" target="_blank" class="text-success small"><i class="fas fa-shield-alt"></i> sbi.bank.in</a></td>
                <td><a href="tel:1800112211">1800 11 2211</a><br><a href="tel:18004253800">1800 425 3800</a></td>
                <td><a href="mailto:customercare@sbi.co.in">customercare@sbi.co.in</a></td>
            </tr>
            <tr>
                <td>186</td>
                <td><b>SAMARTH SAHAKARI BANK LTD; SOLAPUR</b></td>
                <td>Antrolikar Shopping Centre,<br />
Datta Chowk, Solapur - 7</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>187</td>
                <td><b>STANDARD CHARTERED BANK</b></td>
                <td>90, M.G. Road,<br />
MUMBAI - 400 001</td>
                <td><a href="https://www.sc.bank.in/" target="_blank" class="text-primary font-weight-bold">sc.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>188</td>
                <td><b>THE SURAT DISTRICT CO-OPERATIVE BANK LTD.</b></td>
                <td>Kanpith&quot;  Main Branch Nr Chautapool Surat - 395003&quot;</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>189</td>
                <td><b>THE SATARA DISTRICT CENTRAL CO-OPERATIVE BANK LTD</b></td>
                <td>KISAN BHAVAN, C.T.S. NO 523 A/1, PLOT NO 5 AND 6, OPP ZILLA PARISHAD, SADARBAZAR CAMP,<br />
P.B.NO.6, SATARA-415001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>190</td>
                <td><b>SREE CHARAN SOUHARDHA CO OPERATIVE BANK LTD</b></td>
                <td>87/1<br />
H.B. SAMAJA ROAD<br />
GANDHI BAZAR<br />
BASAVANAGUDI<br />
BENGALURU</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>191</td>
                <td><b>SHINHAN BANK</b></td>
                <td>5th Floor, West Wing, <br />
Wockhardt Tower, <br />
BKC, Bandra(E), <br />
Mumbai - 400051</td>
                <td><a href="https://in.shinhanglobal.com/" target="_blank" class="text-primary font-weight-bold">in.shinhanglobal.com</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>192</td>
                <td><b>SOUTH INDIAN BANK</b></td>
                <td>DICT, SIB Building,<br />
Infopark Road, Rajagiri Valley,<br />
P.O,Kakkanad-682039, <br />
Ernakulam, Kerala</td>
                <td><a href="https://www.southindianbank.bank.in/" target="_blank" class="text-primary font-weight-bold">southindianbank.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>193</td>
                <td><b>Sindhudurg District Central Cooperative Bank Ltd.</b></td>
                <td>Plot No.32, Navnagar Vikas Pradhikaran, At &amp; Post- Sindhudurgnagari, Taluka-Kudal, District-Sindhudurg, Maharashtra-416812</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>194</td>
                <td><b>SOLAPUR JANATA SAHAKARI BANK LTD.</b></td>
                <td>GaganBharari  Shivsmark Sunkul goldfinch peth, Solapur - 413007. Mahrashtra. India.</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>195</td>
                <td><b>Shree Kadi Nagarik Sahakari Bank Limited</b></td>
                <td>Gunja Bazar,Kadi-384440, Dist. Mehsana, Gujarat</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>196</td>
                <td><b>SHIKSHAK SAHAKARI BANK LTD.</b></td>
                <td>SHIKSHAK SAHAKARI BANK LTD., GANDHISAGAR, MAHAL, NAGPUR-440018</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>197</td>
                <td><b>SUMITOMO MITSUI BANKING CORPORATION</b></td>
                <td>12th &amp; 13th Floor, <br />
Hindustan Times House, <br />
18-20, Kasturba Gandhi Marg, <br />
New Delhi - 110001</td>
                <td><a href="http://www.smbc.co.jp/global/india/" target="_blank" class="text-primary font-weight-bold">smbc.co.jp/global/india</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>198</td>
                <td><b>SHIVALIK SMALL FINANCE BANK LTD</b></td>
                <td>13/1207, ANSARI ROAD, SAHARANPUR -247001,UTTAR PRADESH</td>
                <td><a href="https://shivalik.bank.in/" target="_blank" class="text-primary font-weight-bold">shivalik.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>199</td>
                <td><b>Smriti Nagrik Sahakari Bank Maryadit</b></td>
                <td>I3-I4 TIRUPATI NAGAR, STATION ROAD MANDSAUR -458002<br />
MP INDIA</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>200</td>
                <td><b>SARASPUR NAGARIK COOP BANK LTD</b></td>
                <td>1, DARSHAN SOCIETY, STADIUM COMMERCE ROAD, NAVARANGPURA, AHMEDABAD 380009</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>201</td>
                <td><b>SOCIETE GENERALE</b></td>
                <td>Maker Chambers IV, 13th Floor, <br />
Nariman Point, <br />
Mumbai -400 021</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>202</td>
                <td><b>THE SURAT PEOPLE&#039;S CO-OPERATIVE BANK LTD.</b></td>
                <td>Vasudhara Bhavan, Timaliyawad, <br />
Nanpura, Near Gandhi Smruti, <br />
Surat- 395001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>203</td>
                <td><b>THE SARASWAT CO-OPERATIVE BANK LTD.</b></td>
                <td>Treasury Dept.1st Floor, <br />
Bombay Mutual Chambers, <br />
Ambalal Doshi Marg <br />
Fort, Mumbai - 400001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>204</td>
                <td><b>SBM BANK (INDIA) LIMITED</b></td>
                <td>1st Floor Raheja Center, <br />
Free Press Journal Marg, <br />
Nariman Point <br />
Mumbai-400021</td>
                <td><a href="https://www.sbm.bank.in/" target="_blank" class="text-primary font-weight-bold">sbm.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>205</td>
                <td><b>STCI PRIMARY DEALER LTD.</b></td>
                <td>A/B1- 801, Marathon Innova, <br />
Marathon Nextgen Compound, <br />
Off G K Marg, Lower Parel(W), <br />
Mumbai 400013</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>206</td>
                <td><b>SURAT NATIONAL COOPERATIVE BANK LTD</b></td>
                <td>NAVNIDHI&quot;  3/4016  KARVA ROAD  NAVAPURA  SURAT- 395003&quot;</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>207</td>
                <td><b>SURYODAY SMALL FINANCE BANK LTD.</b></td>
                <td>1101, SHARDA TERRACES, PLOT NO. 65, SECXTOR-11, CBD BELAPUR, NAVI MUMBAI-400614</td>
                <td><a href="https://suryoday.bank.in/" target="_blank" class="text-primary font-weight-bold">suryoday.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>208</td>
                <td><b>SUCO SOUHARDA SAHAKARI BANK LTD</b></td>
                <td>SUCO BHAVAN, MOKA ROAD, BELLARY -583103</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>209</td>
                <td><b>THE SUTEX CO-OPERATIVE BANK LTD.</b></td>
                <td>Surajram Bachkaniwala Bhavan&quot; Nr.Navjivan Circle  U.M.Rd. Surat-395017&quot;</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>210</td>
                <td><b>SVC CO-OPERATIVE BANK LTD.</b></td>
                <td>SVC Tower, Jawaharlal Nehru Road, <br />
Vakola, Santacruz (E), <br />
Mumbai - 400055</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>211</td>
                <td><b>SHRI VEERSHAIV CO-OP BANK LTD</b></td>
                <td>517, A/1,<br />
E ward,<br />
Tararani Chowk,<br />
Kolhapur- 416001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>212</td>
                <td><b>THE AKOLA URBAN CO-OPERATIVE BANK LTD.</b></td>
                <td>JANKALYAN,58/59 TOSHNIWAL LAYOUTS,NEAR GOVT.MILK SCHEME,MURTIZAPUR ROAD,AKOLA -444001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>213</td>
                <td><b>The Banaskantha Mercantile Co-operative Bank Ltd.</b></td>
                <td>Old Sardar Gunj Road, Palanpur- 385001, District Banaskantha</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>214</td>
                <td><b>THANE BHARAT SHAKARI BANK LTD.</b></td>
                <td>Head Office, Sahayog Mandir, <br />
Sahayog Mandir Path, <br />
Near Ghantali, Naupada, <br />
Thane (W) - 400602</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>215</td>
                <td><b>The Co-Operative Bank of Rajkot Ltd.</b></td>
                <td>Sahakar Sarita,<br />
Panchnath Road,<br />
Rajkot - 360001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>216</td>
                <td><b>THANE DISTRICT CENTRAL CO-OPERATIVE BANK LTD.</b></td>
                <td>Chhatrapti shivaji Path,<br />
Thane (W) - 400 601</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>217</td>
                <td><b>TUMKUR GRAIN MERCHANTS CO-OPERATIVE BANK LTD.</b></td>
                <td>OPP. GOVT. POLYTECHNIC, <br />
B H ROAD, <br />
TUMKUR - 572103</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>218</td>
                <td><b>Telangana Grameena Bank</b></td>
                <td>H.No. 2-1-520, 2nd Floor,<br />
Vijayasrisai Celestia, St.No. 9,<br />
Shankermuth Road, <br />
Hyderabad, Telangana - 500 044</td>
                <td><a href="http://www.tgb.bank.in/" target="_blank" class="text-primary font-weight-bold">tgb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>219</td>
                <td><b>TJSB SAHAKARI BANK LTD.</b></td>
                <td>Madhukar Bhavan, Road No.16,<br />
Wagle Estate, <br />
Thane(West) -400604</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>220</td>
                <td><b>TAMILNAD MERCANTILE BANK LTD.</b></td>
                <td>57.V.E.Road, Head Office<br />
Thoothukudi- 628 002</td>
                <td><a href="https://www.tmb.bank.in/" target="_blank" class="text-primary font-weight-bold">tmb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>221</td>
                <td><b>The Malad Sahakari Bank Ltd</b></td>
                <td>6, SUJATA<br />
1ST FLOOR<br />
RANI SATI MARG<br />
MALAD EAST MUMBAI 400097</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>222</td>
                <td><b>THE NAWANAGAR COOPERATIVE BANK LTD</b></td>
                <td>HIRJI MISTRY ROAD<br />
OPP. PRANAMI SCHOOL<br />
JAMNAGAR<br />
361 004</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>223</td>
                <td><b>TAMILNADU GRAMA BANK  </b></td>
                <td>6,YERCAUD MAIN ROAD, HASTHAMPATTI,SALEM-636007</td>
                <td><a href="http://www.tngb.bank.in/" target="_blank" class="text-primary font-weight-bold">tngb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>224</td>
                <td><b>THE TAMILNADU STATE APEX CO-OPERATIVE BANK LTD.</b></td>
                <td># 233, N.S.C BOSE ROAD ,<br />
GEORGE TOWN,<br />
CHENNAI</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>225</td>
                <td><b>THE PUNJAB STATE COOPERATIVE BANK LTD</b></td>
                <td>S.C.O. 175-187<br />
SECTOR 34-A<br />
CHANDIGARH</td>
                <td><a href="http://www.pgb.bank.in/" target="_blank" class="text-primary font-weight-bold">pgb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>226</td>
                <td><b>TELANGANA STATE CO OPERATIVE APEX BANK LTD</b></td>
                <td>TROOP BAZAR, HYDERABAD- 500 001</td>
                <td><a href="http://www.tgb.bank.in/" target="_blank" class="text-primary font-weight-bold">tgb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>227</td>
                <td><b>THE SATARA SAHAKARI BANK LIMITED</b></td>
                <td>Bhartiya Krida Mandir, 3rd floor, Naigaon Wadala Road, Wadala, Mumbai-400031</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>228</td>
                <td><b>TEXTILE TRADERS CO-OPERATIVE BANK LIMITED</b></td>
                <td>6,VASANT VIHAR SOCIETY, NEAR MITHAKALI SIX ROADS, NAVRANGPURA,<br />
AHMEDABAD- 380 009</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>229</td>
                <td><b>UNION BANK OF INDIA</b></td>
                <td>RTGS CELL,6 FLOOR,<br />
TREASURY BRANCH, <br />
CENTRAL OFFICE ,<br />
239 ,VIDHAN BHAVAN MARG,<br />
NARIMAN POINT, MUMBAI-400021</td>
                <td><a href="https://www.unionbankofindia.co.in" target="_blank" class="text-primary font-weight-bold">unionbankofindia.co.in</a><br>
<a href="https://unionbank.bank.in" target="_blank" class="text-success small"><i class="fas fa-shield-alt"></i> unionbank.bank.in</a></td>
                <td><a href="tel:1800222244">1800 22 22 44</a><br><a href="tel:18002082244">1800 208 2244</a></td>
                <td><a href="mailto:customercare@unionbankofindia.bank.in">customercare@unionbankofindia.bank.in</a></td>
            </tr>
            <tr>
                <td>230</td>
                <td><b>UCO BANK</b></td>
                <td>Head Office-II, <br />
Department of Information Technology ,<br />
3&amp;4 DD Block, <br />
Sector-I, Saltlake,<br />
Kolkata-700064</td>
                <td><a href="https://www.uco.bank.in/" target="_blank" class="text-primary font-weight-bold">uco.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>231</td>
                <td><b>URBAN CO-OPERATIVE BANK LTD. BAREILLY</b></td>
                <td>BLOCK &#039;A&#039;, DEEN DAYAL PURAM, PILIBHIT MARG, BAREILLY - UTTAR PRADESH, 243122</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>232</td>
                <td><b>Ujjivan Small Finance Bank Limited</b></td>
                <td>Plot No.2364/8,<br />
Khampur Raya Village,<br />
Shadikhampur,<br />
Main Patel Road,<br />
New Delhi - 110008</td>
                <td><a href="https://www.ujjivansfb.bank.in/" target="_blank" class="text-primary font-weight-bold">ujjivansfb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>233</td>
                <td><b>Unity Small Finance Bank Ltd.</b></td>
                <td>Centrum House, CST Road, Vidyanagari Marg, Kalina, Santacruz (East), Mumbai-400098, Maharashtra</td>
                <td><a href="https://unity.bank.in/" target="_blank" class="text-primary font-weight-bold">unity.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>234</td>
                <td><b>UNIITED OVERSEAS BANK MUMBAI BRANCH</b></td>
                <td>3, North Avenue, Maker Maxity,<br />
Unit 31 &amp; 37, 3rd floor, &#039;C&#039; Wing,<br />
Bandra - Kurla Complex, Bandra East,<br />
Mumbai 400051</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>235</td>
                <td><b>UTTAR PRADESH COOPERATIVE BANK LTD LUCKNOW</b></td>
                <td>2-MAHATMA GANDHI MARG, HAZRATGANJ, LUCKNOW</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>236</td>
                <td><b>URBAN COOPERATIVE BANK LTD No 1758 PERINTHALMANNA</b></td>
                <td>PB No. 10 PERINTHALMANNA, MALAPPURAM (dist) KERALA- 679322</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>237</td>
                <td><b>UTTARAKHAND STATE CO-OPERATIVE BANK LTD.</b></td>
                <td>ADMIN BUILDING, NEAR SARAS<br />
MARKET, NAINITAL ROAD,<br />
HALDWANI, UTTARAKHAND<br />
263139</td>
                <td><a href="https://ukgb.bank.in/" target="_blank" class="text-primary font-weight-bold">ukgb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>238</td>
                <td><b>AXIS BANK</b></td>
                <td>Information Technology - Second Floor,<br />
Corporate Office,<br />
Bombay Dyeing Mills Compound,<br />
Pandurang Budhkar Marg,<br />
Worli, Mumbai - 400 025</td>
                <td><a href="https://www.axisbank.com" target="_blank" class="text-primary font-weight-bold">axisbank.com</a><br>
<a href="https://axisbank.bank.in" target="_blank" class="text-success small"><i class="fas fa-shield-alt"></i> axisbank.bank.in</a></td>
                <td><a href="tel:18604195555">1860 419 5555</a><br><a href="tel:18605005555">1860 500 5555</a></td>
                <td><a href="mailto:customer.services@axisbank.com">customer.services@axisbank.com</a></td>
            </tr>
            <tr>
                <td>239</td>
                <td><b>UTKARSH SMALL FINANCE BANK LTD</b></td>
                <td>S-24/1-2, FIRST FLR,MAHAVIR NAGAR, ORDERLY BAZAR, VARANASI, UTTAR PRADESH-221 002</td>
                <td><a href="https://www.utkarsh.bank.in/" target="_blank" class="text-primary font-weight-bold">utkarsh.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>240</td>
                <td><b>THE UDAIPUR URBAN CO-OP. BANK LTD.</b></td>
                <td>1ST FLOOR,9C-A,MADHUBAN, UDAIPUR-313004</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>241</td>
                <td><b>THE VARACHHA CO-OPERATIVE BANK LTD., SURAT</b></td>
                <td>Affil Tower, <br />
L.H.Road, <br />
Surat</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>242</td>
                <td><b>Vasai Janata Sahakari Bank Ltd.</b></td>
                <td>Mrudugandha, Azad Raod, Parnaka, Vasai-401201</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>243</td>
                <td><b>THE VIJAY CO-OPERATIVE BANK LTD.</b></td>
                <td>&#039;NOBLES&#039;, OPP. NEHRUBRIDGE,<br />
ASHRAM ROAD<br />
AHMEDABAD - 380009</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>244</td>
                <td><b>VIKAS SOUHARDA CO-OPERATIVE BANK LTD.</b></td>
                <td>STATION ROAD, OPP SRIRAMULU PARK, HOSAPETE<br />
KARNATAKA - 583 201</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>245</td>
                <td><b>THE VISHWESHWAR SAHAKARI BANK LTD., PUNE</b></td>
                <td>471/472, Gultekdi, <br />
Market Yard, <br />
Pune 411 037</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>246</td>
                <td><b>JSC VTB Bank</b></td>
                <td>The Taj Mahal Hotel,<br />
The lobby Mezzanine Floor,<br />
1 Man Singh Road<br />
New Delhi - 110011</td>
                <td><a href="https://vtb.bank.in/" target="_blank" class="text-primary font-weight-bold">vtb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>247</td>
                <td><b>VASAI VIKAS SAHAKARI BANK LTD.</b></td>
                <td>SAMAJ MANDIR, <br />
OPP. NEW ENGLISH SCHOOL, <br />
VASAI (W), MUMBAI 401 201</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>248</td>
                <td><b>THE WEST BENGAL STATE CO-OPERATIVE BANK LTD.</b></td>
                <td>24A Waterloo Street,<br />
Kolkata-700 069<br />
West Bengal</td>
                <td><a href="https://www.wbgb.bank.in/" target="_blank" class="text-primary font-weight-bold">wbgb.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>249</td>
                <td><b>NSE Clearing Limited</b></td>
                <td>EXCHANGE PLAZA, <br />
PLOT NO  C/1, G BLOCK,<br />
BANDRA-KURLA COMPLEX,<br />
BANDRA (E), MUMBAI 400051</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>250</td>
                <td><b>YES BANK LTD</b></td>
                <td>Nehru Centre, 9th floor,<br />
Discovery of India, <br />
Dr. A.B. Road, Worli,<br />
Mumbai - 400 018</td>
                <td><a href="https://www.yes.bank.in/" target="_blank" class="text-primary font-weight-bold">yes.bank.in</a></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>251</td>
                <td><b>THE ZOROASTRIAN COOPERATIVE BANK LIMITED</b></td>
                <td>NIRLON HOUSE, 5TH FLOOR, DR. ANNIE BESANT ROAD, WORLI, MUMBAI-400030</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>252</td>
                <td><b>ZILA SAHKARI BANK LTD GHAZIABAD</b></td>
                <td>Principal Office, R.D.C.A/20,<br />
Pajnagar, Ghaziabad, UP</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
        </tbody>
    </table>
</div>
				</div>
			</div>
		</div>

		<div class="row mt-3 mb-5">
			<div class="col-lg-12">
				<p class="text-muted small text-center" style="font-size: 13px;">* Disclaimer: The contact details (Email, Website, Toll-Free Number) provided above are based on our records and mapped from official sources such as the RBI, but they are subject to change. Please verify with the official bank before making any transactions.</p>
			</div>
		</div>

		<!-- Call to Action -->
		<div class="row justify-content-center text-center mt-4">
			<div class="col-lg-8">
				<div style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.15); border-radius: 16px; padding: 30px;">
					<h4 style="font-family: var(--font-heading); font-weight: 700; color: var(--text-main); margin-bottom: 10px;">Find IFSC codes for these banks</h4>
					<p class="text-muted" style="font-size: 14.5px; margin-bottom: 20px;">Instantly lookup any bank branch location and secure your online NEFT / RTGS transfers.</p>
					<div class="d-flex justify-content-center flex-wrap">
						<a href="<?php echo $base_url; ?>bank" class="btn btn-primary m-2"><i class="fas fa-university mr-2"></i> Bank Directory</a>
						<a href="<?php echo $base_url; ?>bank-ifsc" class="btn btn-secondary m-2"><i class="fas fa-search mr-2"></i> Search IFSC Code</a>
					</div>
				</div>
			</div>
		</div>
		
	</div>
</section>

<script>
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('#bankDataTable').DataTable({
            responsive: true,
            pageLength: 20,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search Banks or IFSC..."
            }
        });
    }
});
</script>

<?php require_once ('includes/footer.php'); ?>
