<?php
/**
 *
 *
 * @author Roman Piták <roman@pitak.net>
 *
 */


namespace DotMailer\Api;


use DotMailer\Api\DataTypes\ApiAccount;
use DotMailer\Api\DataTypes\ApiAddressBook;
use DotMailer\Api\DataTypes\ApiAddressBookList;
use DotMailer\Api\DataTypes\ApiCampaign;
use DotMailer\Api\DataTypes\ApiCampaignContactClickList;
use DotMailer\Api\DataTypes\ApiCampaignContactOpenList;
use DotMailer\Api\DataTypes\ApiCampaignContactPageViewList;
use DotMailer\Api\DataTypes\ApiCampaignContactReplyList;
use DotMailer\Api\DataTypes\ApiCampaignContactRoiDetailList;
use DotMailer\Api\DataTypes\ApiCampaignContactSocialBookmarkViewList;
use DotMailer\Api\DataTypes\ApiCampaignContactSummary;
use DotMailer\Api\DataTypes\ApiCampaignContactSummaryList;
use DotMailer\Api\DataTypes\ApiCampaignList;
use DotMailer\Api\DataTypes\ApiCampaignSend;
use DotMailer\Api\DataTypes\ApiCampaignSummary;
use DotMailer\Api\DataTypes\ApiContact;
use DotMailer\Api\DataTypes\ApiContactImport;
use DotMailer\Api\DataTypes\ApiContactImportReport;
use DotMailer\Api\DataTypes\ApiContactList;
use DotMailer\Api\DataTypes\ApiContactResubscription;
use DotMailer\Api\DataTypes\ApiContactSuppression;
use DotMailer\Api\DataTypes\ApiContactSuppressionList;
use DotMailer\Api\DataTypes\ApiDocument;
use DotMailer\Api\DataTypes\ApiDocumentList;
use DotMailer\Api\DataTypes\ApiFileMedia;
use DotMailer\Api\DataTypes\ApiResubscribeResult;
use DotMailer\Api\DataTypes\ApiTransactionalDataList;
use DotMailer\Api\DataTypes\Guid;
use DotMailer\Api\DataTypes\Int32List;
use DotMailer\Api\DataTypes\XsBoolean;
use DotMailer\Api\DataTypes\XsDateTime;
use DotMailer\Api\DataTypes\XsInt;
use DotMailer\Api\DataTypes\XsString;
use DotMailer\Api\Rest\IClient;


final class Resources implements IResources {

	/** @var IClient */
	private $restClient;

	public function __construct(IClient $restClient) {
		$this->restClient = $restClient;
	}


	/**
	 * @param string $url
	 * @param string $method
	 * @param string $data
	 * @return string
	 */
	private function execute($url, $method = 'GET', $data = null) {
		return $this->restClient->execute(array($url, $method, $data), array());
	}

	/*
	 * ========== RESOURCES ==========
	 */

	/*
	 * ========== account-info ==========
	 */

	public function GetAccountInfo() {
		return new ApiAccount($this->execute('account-info'));
	}

	/*
	 * ========== address-books ==========
	 */

	public function PostAddressBooks(ApiAddressBook $addressBook) {
		return new ApiAddressBook($this->execute('address-books', 'POST', $addressBook->toJson()));
	}

	public function GetAddressBookCampaigns($addressBookId, $select = 1000, $skip = 0) {
		$url = sprintf("address-books/%s/campaigns?select=%s&skip=%s", $addressBookId, $select, $skip);
		new ApiCampaignList($this->execute($url));
	}

	public function DeleteAddressBookContacts($addressBookId) {
		$this->execute(sprintf("address-books/%s/contacts", $addressBookId), 'DELETE');
	}

	public function PostAddressBookContacts($addressBookId, ApiContact $apiContact) {
		$url = sprintf("address-books/%s/contacts", $addressBookId);
		return new ApiContact($this->execute($url, 'POST', $apiContact->toJson()));
	}

	public function DeleteAddressBookContact($addressBookId, $apiContactId) {
		$url = sprintf("address-books/%s/contacts/%s", $addressBookId, $apiContactId);
		$this->execute($url, 'DELETE');
	}

	public function PostAddressBookContactsDelete($addressBookId, $contactIdList) {
		$url = sprintf("address-books/%s/contacts/delete", $addressBookId);
		$this->execute($url, 'DELETE', $contactIdList->toJson());
	}

	public function PostAddressBookContactsImport($addressBookId, ApiFileMedia $apiFileMedia) {
		$url = sprintf("address-books/%s/contacts/import", $addressBookId);
		return new ApiContactImport($this->execute($url, 'POST', $apiFileMedia->toJson()));
	}

	public function GetAddressBookContactsModifiedSinceDate($addressBookId, $date, $withFullData = false, $select = 1000, $skip = 0) {
		$withFullData = $withFullData ? 'true' : 'false';
		$url = sprintf("address-books/%s/contacts/modified-since/%s?withFullData=%s&select=%s&skip=%s", $addressBookId, $date, $withFullData, $select, $skip);
		return new ApiContactList($this->execute($url));
	}

	public function PostAddressBookContactsResubscribe($addressBookId, ApiContactResubscription $apiContactResubscription) {
		$url = sprintf("address-books/%s/contacts/resubscribe", $addressBookId);
		return new ApiResubscribeResult($this->execute($url, 'POST', $apiContactResubscription->toJson()));
	}

	public function PostAddressBookContactsUnsubscribe($addressBookId, ApiContact $apiContact) {
		$url = sprintf("address-books/%s/contacts/unsubscribe", $addressBookId);
		return new ApiContactSuppression($this->execute($url, 'POST', $apiContact->toJson()));
	}

	public function GetAddressBookContactsUnsubscribedSinceDate($addressBookId, $date, $select = 1000, $skip = 0) {
		$url = sprintf("address-books/%s/contacts/unsubscribed-since/%s?select=%s&skip=%s", $addressBookId, $date, $select, $skip);
		new ApiContactSuppressionList($this->execute($url));
	}

	public function GetAddressBookContacts($addressBookId, $withFullData = false, $select = 1000, $skip = 0) {
		$withFullData = $withFullData ? 'true' : 'false';
		$url = sprintf("address-books/%s/contacts?withFullData=%s&select=%s&skip=%s", $addressBookId, $withFullData, $select, $skip);
		return new ApiContactList($this->execute($url));
	}

	public function GetAddressBookById($addressBookId) {
		return new ApiAddressBook($this->execute(sprintf("address-books/%s", $addressBookId)));
	}

	public function UpdateAddressBook(ApiAddressBook $apiAddressBook) {
		$url = sprintf("address-books/%s", $apiAddressBook->id);
		return new ApiAddressBook($this->execute($url, 'PUT', $apiAddressBook->toJson()));
	}

	public function DeleteAddressBook($addressBookId) {
		$this->execute(sprintf("address-books/%s", $addressBookId), 'DELETE');
	}

	public function GetAddressBooksPrivate($select = 1000, $skip = 0) {
		return new ApiAddressBookList($this->execute(sprintf("address-books/private?select=%s&skip=%s", $select, $skip)));
	}

	public function GetAddressBooksPublic($select = 1000, $skip = 0) {
		return new ApiAddressBookList($this->execute(sprintf("address-books/public?select=%s&skip=%s", $select, $skip)));
	}

	public function GetAddressBooks($select = 1000, $skip = 0) {
		return new ApiAddressBookList($this->execute(sprintf("address-books?select=%s&skip=%s", $select, $skip)));
	}

	/*
	 * ========== campaigns ==========
	 */

	public function PostCampaigns(ApiCampaign $apiCampaign) {
		return new ApiCampaign($this->execute('campaigns', 'POST', $apiCampaign->toJson()));
	}

	public function GetCampaignActivityByContactId($campaignId, $contactId) {
		return new ApiCampaignContactSummary($this->execute(sprintf("campaigns/%s/activities/%s", $campaignId, $contactId)));
	}

	public function GetCampaignActivityClicks($campaignId, $contactId, $select = 1000, $skip = 0) {
		$url = sprintf("campaigns/%s/activities/%s/clicks?select=%s&skip=%s", $campaignId, $contactId, $select, $skip);
		return new ApiCampaignContactClickList($this->execute($url));
	}

	public function GetCampaignActivityOpens($campaignId, $contactId, $select = 1000, $skip = 0) {
		$url = sprintf("campaigns/%s/activities/%s/opens?select=%s&skip=%s", $campaignId, $contactId, $select, $skip);
		return new ApiCampaignContactOpenList($this->execute($url));
	}

	public function GetCampaignActivityPageViews($campaignId, $contactId, $select = 1000, $skip = 0) {
		$url = sprintf("campaigns/%s/activities/%s/opens?select=%s&skip=%s", $campaignId, $contactId, $select, $skip);
		return new ApiCampaignContactPageViewList($this->execute($url));
	}

	public function GetCampaignActivityReplies($campaignId, $contactId, $select = 5, $skip = 0) {
		$url = sprintf("campaigns/%s/activities/%s/replies?select=%s&skip=%s", $campaignId, $contactId, $select, $skip);
		return new ApiCampaignContactReplyList($this->execute($url));
	}

	public function GetCampaignActivityRoiDetails($campaignId, $contactId, $select = 1000, $skip = 0) {
		$url = sprintf("campaigns/%s/activities/%s/roi-details?select=%s&skip=%s", $campaignId, $contactId, $select, $skip);
		return new ApiCampaignContactRoiDetailList($this->execute($url));
	}

	public function GetCampaignActivitySocialBookmarkViews($campaignId, $contactId, $select = 1000, $skip = 0) {
		$url = sprintf("campaigns/%s/activities/%s/social-bookmark-views?select=%s&skip=%s", $campaignId, $contactId, $select, $skip);
		return new ApiCampaignContactSocialBookmarkViewList($this->execute($url));
	}

	public function GetCampaignActivitiesSinceDateByDate($campaignId, $dateTime, $select = 1000, $skip = 0) {
		$url = sprintf("campaigns/%s/activities/since-date/%s?select=%s&skip=%s", $campaignId, $dateTime, $select, $skip);
		return new ApiCampaignContactSummaryList($this->execute($url));
	}

	public function GetCampaignActivities($campaignId, $select = 1000, $skip = 0) {
		$url = sprintf("campaigns/%s/activities?select=%s&skip=%s", $campaignId, $select, $skip);
		return new ApiCampaignContactSummaryList($this->execute($url));
	}

	public function GetCampaignAddressBooks($campaignId, $select = 1000, $skip = 0) {
		$url = sprintf("campaigns/%s/address-books?select=%s&skip=%s", $campaignId, $select, $skip);
		return new ApiAddressBookList($this->execute($url));
	}

	public function GetCampaignAttachments($campaignId) {
		$url = sprintf("campaigns/%s/attachments", $campaignId);
		return new ApiDocumentList($this->execute($url));
	}

	public function PostCampaignAttachments($campaignId, ApiDocument $apiDocument) {
		$url = sprintf("campaigns/%s/attachments", $campaignId);
		return new ApiDocument($this->execute($url, 'POST', $apiDocument->toJson()));
	}

	public function DeleteCampaignAttachment($campaignId, $documentId) {
		$this->execute(sprintf("campaigns/%s/attachments/%s", $campaignId, $documentId));
	}

	public function GetCampaignClicks($campaignId, $select = 1000, $skip = 0) {
		$url = sprintf("campaigns/%s/clicks?select=%s&skip=%s", $campaignId, $select, $skip);
		return new ApiCampaignContactClickList($this->execute($url));
	}

	public function PostCampaignCopy($campaignId) {
		$url = sprintf("campaigns/%s/copy", $campaignId);
		return new ApiCampaign($this->execute($url, 'POST'));
	}

	public function GetCampaignHardBouncingContacts($campaignId, $withFullData = false, $select = 1000, $skip = 0) {
		$withFullData = $withFullData ? 'true' : 'false';
		$url = sprintf("/campaigns/%s/hard-bouncing-contacts?withFullData=%s&select=%s&skip=%s", $campaignId, $withFullData, $select, $skip);
		return new ApiContactList($this->execute($url));
	}

	public function GetCampaignOpens($campaignId, $select = 1000, $skip = 0) {
		$url = sprintf("campaigns/%s/opens?select=%s&skip=%s", $campaignId, $select, $skip);
		return new ApiCampaignContactOpenList($this->execute($url));
	}

	public function GetCampaignPageViewsSinceDateByDate($campaignId, $dateTime, $select = 1000, $skip = 0) {
		$url = sprintf("campaigns/%s/page-views/since-date/%s?select=%s&skip=%s", $campaignId, $dateTime, $select, $skip);
		return new ApiCampaignContactPageViewList($this->execute($url));
	}

	public function GetCampaignRoiDetailsSinceDateByDate($campaignId, $dateTime, $select = 1000, $skip = 0) {
		$url = sprintf("campaigns/%s/roi-details/since-date/%s?select=%s&skip=%s", $campaignId, $dateTime, $select, $skip);
		return new ApiCampaignContactRoiDetailList($this->execute($url));
	}

	public function GetCampaignSocialBookmarkViews($campaignId, $select = 1000, $skip = 0) {
		$url = sprintf("campaigns/%s/social-bookmark-views?select=%s&skip=%s", $campaignId, $select, $skip);
		return new ApiCampaignContactSocialBookmarkViewList($this->execute($url));
	}

	public function GetCampaignSummary($campaignId) {
		return new ApiCampaignSummary($this->execute(sprintf("campaigns/%s/summary", $campaignId)));
	}

	public function GetCampaignById($campaignId) {
		return new ApiCampaign($this->execute(sprintf("campaigns/%s", $campaignId)));
	}

	public function UpdateCampaign(ApiCampaign $apiCampaign) {
		return new ApiCampaign($this->execute(sprintf("campaigns/%s", $apiCampaign->id), 'PUT', $apiCampaign));
	}

	public function PostCampaignsSend(ApiCampaignSend $apiCampaignSend) {
		return new ApiCampaignSend('campaigns/send', 'POST', $apiCampaignSend->toJson());
	}

	public function GetCampaignsSendBySendId($sendId) {
		return new ApiCampaignSend(sprintf("campaigns/send/%s", $sendId));
	}

	public function GetCampaignsWithActivitySinceDate($dateTime, $select = 1000, $skip = 0) {
		$url = sprintf("campaigns/with-activity-since/%s?select=%s&skip=%s", $dateTime, $select, $skip);
		return new ApiCampaignList($this->execute($url));
	}

	public function GetCampaigns($select = 1000, $skip = 0) {
		return new ApiCampaignList($this->execute(sprintf("campaigns?select=%s&skip=%s", $select, $skip)));
	}


	/*
	 * ========== contacts ==========
	 */

	public function PostContacts(ApiContact $apiContact) {
		return new ApiContact($this->execute('contacts', 'POST', $apiContact->toJson()));
	}

	public function GetContactAddressBooks($contactId, $select = 1000, $skip = 0) {
		$url = sprintf("contacts/%s/address-books?select=%s&skip=%s", $contactId, $select, $skip);
		return new ApiAddressBookList($this->execute($url));
	}

	public function GetContactByEmail($email) {
		return new ApiContact($this->execute(sprintf("contacts/%s", $email)));
	}

	public function DeleteContactTransactionalData($contactId, $collectionName) {
		$url = sprintf("contacts/%s/transactional-data/%s", $contactId, $collectionName);
		$this->execute($url, 'DELETE');
	}

	public function GetContactTransactionalDataByCollectionName($contactId, $collectionName) {
		$url = sprintf("contacts/%s/transactional-data/%s", $contactId, $collectionName);
		return new ApiTransactionalDataList($this->execute($url));
	}

	public function GetContactById($contactId) {
		return new ApiContact($this->execute(sprintf("contacts/%s", $contactId)));
	}

	public function UpdateContact(ApiContact $apiContact) {
		$url = sprintf("contacts/%s", $apiContact->id);
		return new ApiContact($this->execute($url, 'PUT', $apiContact->toJson()));
	}

	public function DeleteContact($contactId) {
		$this->execute(sprintf("contacts/%s", $contactId), 'DELETE');
	}

	public function GetContactsCreatedSinceDate($date, $withFullData = false, $select = 1000, $skip = 0) {
		$withFullData = $withFullData ? 'true' : 'false';
		$url = sprintf("contacts/created-since/%s?withFullData=%s&select=%s&skip=%s", $date, $withFullData, $select, $skip);
		return new ApiContactList($this->execute($url));
	}

	public function PostContactsImport(ApiFileMedia $apiFileMedia) {
		return new ApiContactImport($this->execute('contacts/import', 'POST', $apiFileMedia->toJson()));
	}

	public function GetContactsImportByImportId($importId) {
		$url = sprintf("contacts/import/%s", $importId);
		return new ApiContactImport($this->execute($url));
	}

	public function GetContactsImportReport($importId) {
		$url = sprintf("contacts/import/%s/report", $importId);
		return new ApiContactImportReport($this->execute($url));
	}

	// todo GetContactsImportReportFaults()

	public function GetContactsModifiedSinceDate($date, $withFullData = false, $select = 1000, $skip = 0) {
		$withFullData = $withFullData ? 'true' : 'false';
		$url = sprintf("contacts/modified-since/%s?withFullData=%s&select=%s&skip=%s", $date, $withFullData, $select, $skip);
		return new ApiContactList($this->execute($url));
	}

	public function PostContactsResubscribe(ApiContactResubscription $apiContactResubscription) {
		return new ApiResubscribeResult($this->execute('contacts/resubscribe', 'POST', $apiContactResubscription->toJson()));
	}

	public function GetContactsSuppressedSinceDate($date, $select = 1000, $skip = 0) {
		$url = sprintf("contacts/suppressed-since/%s?select=%s&skip=%s", $date, $select, $skip);
		return new ApiContactSuppressionList($this->execute($url));
	}


	/*
	 * ========== transactional-data ==========
	 */


}


































