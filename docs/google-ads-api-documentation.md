# Google Ads API Usage Documentation

## 1. Application Overview

**Application Name:** Controling - Business Analytics Platform
**Developer:** Cégem 360 Kft.
**Application Type:** Internal Business Tool
**API Access Level Requested:** Basic Access

## 2. Company Description

Our company provides a business analytics and controlling platform that helps businesses monitor and analyze their digital marketing performance. The platform aggregates data from multiple sources including Google Ads, Google Analytics, and Google Search Console to provide comprehensive performance insights.

## 3. How We Use Google Ads API

### 3.1 Purpose

We use the Google Ads API exclusively for **read-only** data retrieval purposes. Our application imports advertising campaign performance data to create unified dashboards and reports for our clients.

**We do NOT:**
- Create, modify, or delete campaigns
- Manage bids or budgets
- Create or modify ads
- Make any changes to Google Ads accounts

### 3.2 Data We Access

| Resource | Fields Retrieved | Purpose |
|----------|-----------------|---------|
| Campaigns | id, name, status | Campaign identification |
| Ad Groups | id, name, status | Ad group level reporting |
| Metrics | impressions, clicks, cost_micros, conversions, ctr, average_cpc, conversions_value, cost_per_conversion | Performance analysis |
| Segments | date, hour, device | Time-based and device reporting |
| Demographics | gender, age_range | Audience insights |
| Geographic View | country_criterion_id, location_type | Regional performance |

### 3.3 API Methods Used

We only use the following read-only method:

```
GoogleAdsService.Search
```

This method is used to execute GAQL (Google Ads Query Language) queries to retrieve performance data.

## 4. Sample GAQL Queries

### 4.1 Campaign Performance Query

```sql
SELECT
    segments.date,
    campaign.id,
    campaign.name,
    campaign.status,
    metrics.impressions,
    metrics.clicks,
    metrics.cost_micros,
    metrics.average_cpc,
    metrics.ctr,
    metrics.conversions,
    metrics.conversions_value,
    metrics.cost_per_conversion
FROM campaign
WHERE segments.date BETWEEN '2024-01-01' AND '2024-03-31'
ORDER BY segments.date DESC
```

### 4.2 Ad Group Performance Query

```sql
SELECT
    segments.date,
    campaign.id,
    campaign.name,
    ad_group.id,
    ad_group.name,
    ad_group.status,
    metrics.impressions,
    metrics.clicks,
    metrics.cost_micros,
    metrics.conversions
FROM ad_group
WHERE segments.date BETWEEN '2024-01-01' AND '2024-03-31'
ORDER BY segments.date DESC
```

### 4.3 Device Performance Query

```sql
SELECT
    segments.date,
    segments.device,
    metrics.impressions,
    metrics.clicks,
    metrics.cost_micros,
    metrics.conversions
FROM campaign
WHERE segments.date BETWEEN '2024-01-01' AND '2024-03-31'
```

### 4.4 Demographics Query

```sql
SELECT
    segments.date,
    ad_group_criterion.gender.type,
    metrics.impressions,
    metrics.clicks,
    metrics.cost_micros,
    metrics.conversions
FROM gender_view
WHERE segments.date BETWEEN '2024-01-01' AND '2024-03-31'
```

### 4.5 Geographic Performance Query

```sql
SELECT
    segments.date,
    geographic_view.country_criterion_id,
    geographic_view.location_type,
    metrics.impressions,
    metrics.clicks,
    metrics.cost_micros,
    metrics.conversions
FROM geographic_view
WHERE segments.date BETWEEN '2024-01-01' AND '2024-03-31'
```

## 5. OAuth 2.0 Implementation

### 5.1 Authentication Flow

1. User initiates connection from our Settings page
2. User is redirected to Google's OAuth consent screen
3. User grants permission to access Google Ads data
4. Application receives authorization code
5. Application exchanges code for access and refresh tokens
6. Tokens are securely stored (encrypted) in our database

### 5.2 Scopes Used

```
https://www.googleapis.com/auth/adwords
```

### 5.3 Token Management

- **Access Token Storage:** Encrypted in database using Laravel's encryption
- **Refresh Token Storage:** Encrypted in database
- **Token Refresh:** Automatic refresh before expiration (5-minute buffer)
- **Token Revocation:** Users can disconnect at any time, which revokes the token

### 5.4 Disconnect Feature

Users can disconnect their Google Ads account at any time through our Settings page. When disconnecting:
1. The access token is revoked via Google's API
2. All stored tokens are cleared from our database
3. The connection status is updated to "disconnected"

## 6. Data Handling & Security

### 6.1 Data Storage

- All OAuth tokens are encrypted at rest using AES-256 encryption
- Campaign data is stored in our secure database
- Data is associated with specific team/tenant for multi-tenancy support

### 6.2 Data Retention

- Performance data is retained for reporting purposes
- Users can request data deletion at any time
- Disconnecting removes OAuth credentials immediately

### 6.3 Security Measures

- HTTPS-only communication
- Encrypted credential storage
- Secure session management
- Regular security audits
- No credentials exposed in logs or error messages

## 7. API Usage Patterns

### 7.1 Request Frequency

- **Sync Frequency:** Manual trigger by user (typically 1-4 times per day)
- **Data Range:** Last 90 days of performance data
- **Batch Processing:** Data is retrieved in single queries per resource type

### 7.2 Rate Limiting Compliance

- We implement exponential backoff for retry logic
- We respect Google's rate limits
- We cache retrieved data to minimize API calls

### 7.3 Error Handling

- All API errors are logged for debugging
- Users receive friendly error notifications
- Failed syncs do not affect previously stored data

## 8. User Interface

### 8.1 Settings Page

Users can:
- Connect/disconnect their Google Ads account
- Enter their Customer ID
- Enter their Manager Customer ID (for MCC accounts)
- Trigger manual data synchronization
- View connection status and last sync time

### 8.2 Dashboards

Retrieved data is displayed in:
- Campaign performance tables
- Time-series charts
- Device breakdown reports
- Geographic performance maps
- Demographic insights

## 9. Technical Implementation

### 9.1 Technology Stack

- **Backend:** PHP 8.4, Laravel 12
- **Google Ads Library:** google/ads-google-ads-php v31.x
- **API Version:** Google Ads API v22
- **Database:** SQLite/MySQL with encrypted columns

### 9.2 Code Architecture

```
app/
├── Jobs/
│   └── GoogleAdsImport.php          # Background job for data import
├── Services/
│   ├── GoogleAdsClientFactory.php   # Creates authenticated API client
│   └── GoogleAdsOAuthService.php    # Handles OAuth flow
├── Models/
│   └── GoogleAdsSettings.php        # Stores connection settings
└── Http/Controllers/
    └── GoogleAdsOAuthController.php # Handles OAuth callbacks
```

## 10. Contact Information

**Developer Contact:**
- Name: Zoltán Tamás Szabó
- Email: zoltan@cegem360.hu
- Company: Cégem 360 Kft.
- Website: https://cegem360.hu

**Technical Support:**
- Email: zoltan@cegem360.hu

---

*Document Version: 1.0*
*Last Updated: January 2026*
