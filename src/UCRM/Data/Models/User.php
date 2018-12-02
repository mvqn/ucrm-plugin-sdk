<?php
declare(strict_types=1);

namespace UCRM\Data\Models;

use MVQN\Data\Models\Model;
use MVQN\Data\Annotations\TableNameAnnotation as TableName;
use MVQN\Data\Annotations\ColumnNameAnnotation as ColumnName;

/**
 * Class User
 *
 * @package UCRM\Data
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @TableName user
 *
 * @method int|null         getUserId()
 * @method int|null         getGroupId()
 * @method string|null      getUsername()
 * @method string|null      getEmail()
 * @method string|null      getPassword()
 * @method bool|null        getIsActive()
 * @method string|null      getFirstName()
 * @method string|null      getLastName()
 * @method string|null      getRole()
 * @method string|null      getConfirmationToken()
 * @method \DateTime|null   getPasswordRequestedAt()
 * @method string|null      getFirstLoginToken()
 * @method \DateTime|null   getDeletedAt()
 * @method \DateTime|null   getLastLogin()
 * @method string|null      getLocaleId()
 * @method string|null      getGoogleOAuthToken()
 * @method int|null         getGoogleCalendarId()
 * @method \DateTime|null   getNextGoogleCalendarSynchronization()
 * @method bool|null        getGoogleSynchronizationErrorNotificationSent()
 * @method int|null         getUserPersonalizationId()
 * @method string|null      getGoogleAuthenticatorSecret()
 * @method string|null      getBackupCodes()
 * @method \DateTime|null   getCreatedAt()
 * @method string|null      getAvatarColor()
 * @method int|null         getTwoFactorFailureCount()
 * @method \DateTime|null   getTowFactorFailureResetAt()
 * @method string|null      getFullName()
 */
final class User extends Model
{
    /**
     * @var int
     * @ColumnName user_id
     */
    protected $userId;

    /**
     * @var int
     * @ColumnName group_id
     */
    protected $groupId;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var bool
     * @ColumnName is_active
     */
    protected $isActive;

    /**
     * @var string
     * @ColumnName first_name
     */
    protected $firstName;

    /**
     * @var string
     * @ColumnName last_name
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $role;

    /**
     * @var string
     * @ColumnName confirmation_token
     */
    protected $confirmationToken;

    /**
     * @var \DateTime
     * @ColumnName password_requested_at
     */
    protected $passwordRequestedAt;

    /**
     * @var string
     * @ColumnName first_login_token
     */
    protected $firstLoginToken;

    /**
     * @var \DateTime
     * @ColumnName deleted_at
     */
    protected $deletedAt;

    /**
     * @var \DateTime
     * @ColumnName last_login
     */
    protected $lastLogin;

    /**
     * @var int
     * @ColumnName locale_id
     */
    protected $localeId;

    /**
     * @var string
     * @ColumnName google_oauth_token
     */
    protected $googleOAuthToken;

    /**
     * @var string
     * @ColumnName google_calendar_id
     */
    protected $googleCalendarId;

    /**
     * @var \DateTime
     * @ColumnName next_google_calendar_synchronization
     */
    protected $nextGoogleCalendarSynchronization;

    /**
     * @var bool
     * @ColumnName google_synchronization_error_notification_sent
     */
    protected $googleSynchronizationErrorNotificationSent;

    /**
     * @var int
     * @ColumnName user_personalization_id
     */
    protected $userPersonalizationId;

    /**
     * @var string
     * @ColumnName google_authenticator_secret
     */
    protected $googleAuthenticatorSecret;

    /**
     * @var string
     * @ColumnName backup_codes
     */
    protected $backupCodes;

    /**
     * @var \DateTime
     * @ColumnName created_at
     */
    protected $createdAt;

    /**
     * @var string
     * @ColumnName avatar_color
     */
    protected $avatarColor;

    /**
     * @var int
     * @ColumnName two_factor_failure_count
     */
    protected $twoFactorFailureCount;

    /**
     * @var \DateTime
     * @ColumnName two_factor_failure_reset_at
     */
    protected $twoFactorFailureResetAt;

    /**
     * @var string
     * @ColumnName full_name
     */
    protected $fullName;

}