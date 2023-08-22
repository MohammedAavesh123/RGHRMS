<?php
date_default_timezone_set("Asia/Kolkata");
include app_path().'/Global_constants.php';



Route::group([

  'middleware' => 'api',

  'prefix' => 'auth'

], function () {

  Route::get('hash-password/{skip}/{take}','api\v1\ApiController@HashPassword');

  Route::get('password-hash-cron','api\v1\ApiController@hashPasswordCron');
  Route::get('password-hash-cron-2','api\v1\ApiController@hashPasswordCron2');

  ////////////  JWT TOKEN ROUTES  FROM HERE    ////////////////////////

  Route::post('social-login', 'api\v1\ApiController@socialLogin');

  Route::post('login', 'api\v1\ApiController@login');

  Route::post('login-new', 'api\v1\ApiController@login_new');

  Route::post('razorpay','api\v1\ApiController@razorPay');

  Route::post('tds-new-api', 'api\v1\ApiController@tds_data');

Route::post('all-tds-list', 'api\v1\ApiController@all_tds_list');

  // Route::post('/pssaword/email','api\v1\ApiController@sendResetLinkEmail');

  /////     It starts the process to reset the password

  Route::post('/forget-password','MailController@forgotPassword');

  // Route::post('password/update/','MailController@changePassword');

  Route::post('/password/reset','api\v1\ApiController@reset');

  Route::post('logout', 'api\v1\ApiController@logout');

  Route::post('refresh', 'api\v1\ApiController@refresh');

  Route::post('register', 'api\v1\ApiController@register');

  Route::post('register_new', 'api\v1\ApiController@register_new');
  Route::post('deleteAccount', 'api\v1\ApiController@deleteAccount');


  Route::post('send_new_otp_new','api\v1\ApiController@sendNewOtp_new');

  Route::post('verify_otp_new','api\v1\ApiController@verifyOtp_new');

  Route::post('verify-otp-login','api\v1\ApiController@verifyOtpOnLogin');

  Route::post('verify-otp-register_new','api\v1\ApiController@verifyOtpOnRegister_new');

  Route::post('me','api\v1\ApiController@me');

  Route::post('payload', 'api\v1\ApiController@payload');

  Route::post('forget_user', 'api\v1\ApiController@forgetUser');

  ///////////////////// TO HERE   /////////////////////////////////////

  //Route::post('reset-password', 'api\v1\ApiController@resetPasswordDefault');

  Route::post('alterlogin','api\v1\ApiController@alterlogin');

  //Route::post('checkprivate', 'api\v1\ApiController@checkprivate');

  Route::post('edit-profile', 'api\v1\ApiController@editProfile');
  Route::post('forget-password-new', 'api\v1\ApiController@forgotPassword_new');


  Route::post('resend-password-new', 'api\v1\ApiController@resendOtp_new');
  Route::post('validate-otp-new', 'api\v1\ApiController@validateOTP');
  Route::post('change-password-new', 'api\v1\ApiController@changePassword_new');


  Route::post('edit-email', 'api\v1\ApiController@editEmail');
  Route::post('verify_email_otp', 'api\v1\ApiController@verifyEmailOtp');


  Route::post('create-team', 'api\v1\ApiController@createTeam');

  Route::any('getmatchlist', 'api\v1\ApiController@getMatchList');

  Route::post('getmatchlistupgrade', 'api\v1\ApiController@getMatchListUpgrade');

  Route::post('mybalance','api\v1\ApiController@myBalance');

  Route::post('my-play-history','api\v1\ApiController@myPlayHistory');

  Route::post('myusablebalance','api\v1\ApiController@myUsableBalance');
  Route::post('myusablebalance-android','api\v1\ApiController@myUsableBalance');

  Route::post('mytransaction','api\v1\ApiController@myTransaction');
  Route::post('transaction-download','api\v1\ApiController@transactiondownload');

  Route::post('user-full-details','api\v1\ApiController@userFullDetails');

  Route::post('myjoinedleauges','api\v1\ApiController@myjoinedleagues');

  Route::post('myjoinedleauges-vvp','api\v1\ApiController@myjoinedleagues_vvp');

  Route::any('vvp_showPlayerPoints','api\v1\ApiController@showPlayerPoints');



  Route::post('joinleague-vvp','api\v1\ApiController@joinleague_vvp');




  // Route::post('seealltestimonials', 'api\v1\ApiController@seealltestimonials');

  Route::post('addPromoteBasicDetails','api\v1\ApiController@addPromoteBasicDetails');

  Route::post('matchalljoinedusers', 'api\v1\ApiController@matchalljoinedusers');

  Route::any('myjointeam', 'api\v1\ApiController@myjointeam');

  Route::post('userinfo', 'api\v1\ApiController@userinfo');

  Route::post('all-verify', 'api\v1\ApiController@allVerify');

  Route::post('verify-email', 'MailController@verifyEmail');



  Route::post('see-pan-details', 'api\v1\ApiController@seePanDetails');

  Route::post('seebankdetails', 'api\v1\ApiController@seebankdetails');

  Route::post('email-update','api\v1\ApiController@emailUpdate');

  Route::post('get-banners','api\v1\ApiController@getOffersAndroid');
  Route::post('get-refer-code','api\v1\ApiController@getReferCode');
  Route::post('check-status-razor','api\v1\ApiController@checkRazorStatusTest');
  Route::post('check-status-paytm','api\v1\ApiController@checkPaytmStatusTest');

  Route::post('get_cashfree_checksum','api\v1\ApiController@get_cashfree_checksum');
  Route::post('get_cashfree_token','api\v1\ApiController@get_cashfree_token');
  Route::post('get_paytm_checksum','api\v1\ApiController@get_paytm_checksum');


  Route::post('mobile-update','api\v1\ApiController@mobileUpdate');

  Route::post('change-password', 'api\v1\ApiController@changePassword');

  Route::post('pay-with-paytm', 'api\v1\ApiController@payWithPaytm');

  // Route::post('thistransaction','api\v1\ApiController@thistransaction');

  // Route::post('updateallwallets', 'api\v1\ApiController@updateallwallets');

  // Route::post('mywithrawlist','api\v1\ApiController@mywithrawlist');

  Route::post('request-withdraw','api\v1\ApiController@requestWithdraw');
  Route::post('withdraw-list','api\v1\ApiController@withdrawList');

  // Route::post('add_money_to_wallet', 'api\v1\ApiController@add_money_to_wallet');

  Route::post('bank-verify', 'api\v1\ApiController@bankVerify');

  Route::post('update-profile-image','api\v1\ApiController@updateProfileImage');

  Route::post('remove-profile-image','api\v1\ApiController@removeProfileImage');




  Route::post('refresh-scores-new', 'api\v1\ApiController@refreshScoresNew');

  //Route::post('allmarathons', 'api\v1\ApiController@allmarathons');



  Route::post('add-match-alerts','api\v1\ApiController@addMatchAlerts');
  Route::post('add-cash-banners','api\v1\ApiController@addCashBanners');


  Route::post('send-email-otp','MailController@sendEmailOtp');

  Route::post('activation','api\v1\ApiController@activation');

  Route::post('send-new-mail','api\v1\ApiController@sendNewMail');
  Route::post('verify-mobile','api\v1\ApiController@verifyMobile');

  Route::post('send_new_otp','api\v1\ApiController@sendNewOtp');

  Route::post('verify_otp','api\v1\ApiController@verifyOtp');

  Route::post('verify-otp-register','api\v1\ApiController@verifyOtpOnRegister');

  Route::post('verify-pan-request', 'api\v1\ApiController@verifyPanRequest');

  Route::post('getposters', 'api\v1\ApiController@getposters');

  // Route::post('allseries', 'api\v1\ApiController@allseries');

  // Route::post('jointeamlist1','api\v1\ApiController@jointeamlist1');

  Route::post('joinleague','api\v1\ApiController@joinleague');
  Route::post('joinleague-v2','api\v1\ApiController@joinleague_new_v2');

  Route::post('joinleague-v2-android','api\v1\ApiController@joinleague_new_v2');

  Route::post('jointeamlist','api\v1\ApiController@jointeamlist');

  Route::post('my-team', 'api\v1\ApiController@myTeam');

  // Route::post('getmatchdetails', 'api\v1\ApiController@getmatchdetails');

  Route::any('get-challenges-by-category', 'api\v1\ApiController@getChallengesByCategory');

  Route::any('get-free-teams-by-Challenge', 'api\v1\ApiController@getContestFreeTeamNumber');

  Route::any('category-leagues', 'api\v1\ApiController@categoryLeagues');

  Route::post('verify-promo-code', 'api\v1\ApiController@verifyPromoCode');

  Route::any('android-add-fund-api', 'api\v1\ApiController@androidAddFundApi');
  Route::any('amountDeduct', 'api\v1\ApiController@amountDeduct');

  Route::post('getscorecards', 'api\v1\ApiController@getscorecards');

  Route::post('add_paytm_number', 'api\v1\ApiController@add_paytm_number');

  Route::any('get-challenges-new', 'api\v1\ApiController@getChallengesNew');

  Route::post('get-challenges-new-alter', 'api\v1\ApiController@getChallengesNewMain');

  Route::post('/upload-pan-image-android','api\v1\ApiController@uploadPanImage');

  Route::post('upload-bank-image-android','api\v1\ApiController@uploadBankImageAndroid');

  // hk
  Route::post('/upload-adhar-front-image-android','api\v1\ApiController@uploadAdharImage');
  Route::post('/upload-adhar-back-image-android','api\v1\ApiController@uploadBackAdharImage');

  Route::post('verify-adhar-request', 'api\v1\ApiController@verifyAdharRequest');


  Route::post('countnotification','api\v1\ApiController@countNotification');

  Route::post('usernotifications','api\v1\ApiController@usernotifications');

  Route::post('seennotifications','api\v1\ApiController@seennotifications');

  Route::post('teamsjoin','api\v1\ApiController@teamsjoin');

  Route::post('refercodechallenge','api\v1\ApiController@refercodechallenge');

  Route::post('sendinvite','api\v1\ApiController@sendinvite');

  Route::any('abouttoexpire','api\v1\ApiController@aboutToExpire');

  Route::post('best_team','api\v1\ApiController@bestTeam');

  Route::post('join-by-code','api\v1\ApiController@joinByCode');

  Route::post('getteamtoshow','api\v1\ApiController@getteamtoshow');
  Route::post('compare','api\v1\ApiController@compare');
  Route::post('compare_new','api\v1\ApiController@compare_new');
  Route::post('investments','api\v1\ApiController@investments');

  Route::post('leaderboard','api\v1\ApiController@leaderboard');
  Route::post('get-series','api\v1\ApiController@getSeries');
  Route::post('get-series-leaderboard','api\v1\ApiController@getSeriesLeaderboard');
  Route::post('get-series-leaderboard-new','api\v1\ApiController@getSeriesLeaderboard2');
  Route::post('get-match-leaderboard','api\v1\ApiController@getMatchLeaderboards');

  Route::post('get-promoter-series','api\v1\ApiController@getPromoterSeries');
  Route::post('get-promoter-series-leaderboard-new','api\v1\ApiController@getPromoterSeriesLeaderboard');
  Route::post('get-promoter-match-leaderboard','api\v1\ApiController@getPromoterMatchLeaderboards');

  Route::post('league_detail','api\v1\ApiController@league_detail');

  // Route::post('countmyleagues','api\v1\ApiController@countmyleagues');

  Route::post('leaguedetails','api\v1\ApiController@leagueDetails');

  // Route::post('leaguedetails','api\v1\ApiController@leagueDetailsMain');

  Route::post('myjoinedmatches','api\v1\ApiController@myJoinedMatches');

  Route::post('promoterMatches','api\v1\ApiController@promoterMatches');
  Route::post('promoterContests','api\v1\ApiController@promoterContests');
  Route::post('promoterTeams','api\v1\ApiController@promoterTeams');
  Route::post('promoterTotal','api\v1\ApiController@promoterTotal');


  Route::post('create-challenge','api\v1\ApiController@createChallenge');

  Route::post('updateteamleauge','api\v1\ApiController@updateteamchallenge');

  Route::post('getplayerinfo','api\v1\ApiController@getplayerinfo');

  Route::post('getplayerlist_vvp','api\v1\ApiController@getplayerlist_vvp');

  Route::post('getplayerlist','api\v1\ApiController@getplayerlist');


  Route::any('version', 'api\v1\ApiController@version');

  Route::any('hash-password', 'api\v1\ApiController@Hash_password');



  Route::post('findtopbatsman','api\v1\ApiController@findtopbatsman');

  Route::post('findtopbowlers','api\v1\ApiController@findtopbowlers');

  Route::post('findtopallrounders','api\v1\ApiController@findtopallrounders');

  Route::post('find-join-team','api\v1\ApiController@findJoinTeam');

  Route::post('findjointeamofusers','api\v1\ApiController@findjointeamofusers');

  Route::post('refer-bonus-list','api\v1\ApiController@referBonusList');
  Route::post('refer-bonus-list-new','api\v1\ApiController@referBonusList2');



  Route::post('findtopwk','api\v1\ApiController@findtopwk');

  Route::post('playerfullinfo', 'api\v1\ApiController@playerfullinfo');

  Route::post('live-matches','api\v1\ApiController@liveMatches');

  Route::post('live-score-board','api\v1\ApiController@liveScoreBoard');
  Route::post('live-score-board-all-format','api\v1\ApiController@liveScoreBoardNew');
  Route::post('live-scores','api\v1\ApiController@liveScores');

  Route::post('completedmatch','api\v1\ApiController@completedmatch');

  Route::post('viewscorecard','api\v1\ApiController@viewscorecard');

  Route::post('firstcheckteam','api\v1\ApiController@firstcheckteam');

  Route::post('updateteamname','api\v1\ApiController@updateteamname');

  Route::post('updateteamname_new','api\v1\ApiController@updateteamname_new');
  Route::post('getglobalchallenges','api\v1\ApiController@getglobalchallenges');

  // Route::post('seriesdetails','api\v1\ApiController@seriesdetails');

  // Route::post('marathonmatrix','api\v1\ApiController@marathonmatrix');

  Route::any('matchplayerspoints','api\v1\ApiController@matchplayerspoints');

  Route::post('find-scratch-card','api\v1\ApiController@findScratchCard');
  Route::post('open-scratch-card','api\v1\ApiController@openScratchCard');
  Route::post('scratch-cards-list','api\v1\ApiController@ScratchCardsList');


  Route::post('social_login_alter', 'api\v1\ApiController@social_login_alter');




  Route::post('userlevel', 'api\v1\ApiController@userlevel_new');
  Route::post('myjoinedmatches_live','api\v1\ApiController@myJoinedMatches_live');
  Route::post('myjoinedmatches_finished','api\v1\ApiController@myjoinedmatches_finished');
  Route::post('fav-contest','api\v1\ApiController@favcontest');
  Route::post('getpromocode', 'api\v1\ApiController@getpromocode');

  Route::post('sabpaisa_notify_api','api\v1\ApiController@sabpaisa_notify_api');
  Route::post('get_subpaisa_checksum','api\v1\ApiController@get_subpaisa_checksum');

  Route::post('affilate-commission-adjustment','api\v1\ApiController@affilate_commission_adjustment');



});
