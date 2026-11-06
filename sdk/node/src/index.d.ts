export type Scope = 'catalogue:read' | 'integration:read';
export type Locale = 'en' | 'fr';
export type QuoteOption = 'clinic-saas' | 'clinic-perpetual' | 'hospital-small' | 'hospital-medium-large' | 'specialty-lab' | 'specialty-pharmacy' | 'specialty-radiology' | 'specialty-cardiology' | 'specialty-dental' | 'specialty-dermatology' | 'specialty-pediatrics' | 'specialty-obgyn' | 'care-single-hospital' | 'care-multi-hospital' | 'care-national' | 'health-id-facility' | 'health-id-multi-facility' | 'health-id-national' | 'clinical-cdms-saas' | 'clinical-cdms-perpetual' | 'clinical-triage-saas' | 'clinical-triage-perpetual' | 'clinical-cdss-hospital' | 'clinical-cdss-network' | 'government-insurance' | 'government-public-health' | 'government-national-platform' | 'service-implementation' | 'service-data-migration' | 'service-training' | 'service-custom-integration' | 'maintenance-standard' | 'maintenance-premium' | 'maintenance-enterprise' | 'managed-hosting' | 'custom-proposal';
export interface IntegrationApplicationRequest {
  application_name: string;
  access_type: 'machine' | 'delegated' | 'both';
  redirect_uri?: string | null;
  requested_scopes?: string[];
  use_case: string;
}
export type { BusinessOperationId } from './business-operations.js';
import type { BusinessOperationId } from './business-operations.js';
export interface EnquiryInput {
  name: string; email: string; phone?: string | null; facility_type?: string | null;
  products?: string | null; message?: string | null; product_slug?: string | null; locale?: Locale; quote_option?: QuoteOption;
  integration_application?: IntegrationApplicationRequest;
}
export interface CatalogueResponse {
  data: Array<{ slug: string; name: string; category: string; url: string }>;
  meta: { page: number; per_page: number; total: number };
}
export interface IntegrationResponse {
  data: { client_id: string; name: string; scopes: Scope[]; requests_today: number };
}
export interface IntegrationUsageResponse {
  data: {
    period_start: string; period_end: string; requests_today: number; requests_last_30_days: number;
    daily_usage: Array<{ date: string; requests: number }>;
  };
}
export interface IntegrationQuotaResponse {
  data: {
    period: string; requests_today: number; requests_this_month: number;
    daily_limit: number | null; daily_remaining: number | null; daily_resets_at: string;
    monthly_limit: number | null; monthly_remaining: number | null; monthly_resets_at: string;
  };
}
export interface ServiceOfferingsResponse {
  data: { locale: Locale; items: Array<{
    id: 'health-data' | 'cdss'; name: string; summary: string; icon: string; cta: string;
    data_boundary: string; contact_url: string;
  }> };
}
export interface DeveloperOAuthApplicationInput {
  name: string;
  redirect_uris: string[];
  scopes: string[];
}
export interface PartnerApplicationInput {
  organization_name: string;
  contact_name: string;
  email: string;
  phone?: string;
  country: string;
  city?: string;
  partner_type: 'reseller' | 'technology' | 'implementation' | 'referral' | 'hospital' | 'ngo' | 'health_ai' | 'investor';
  organization_type?: string;
  annual_revenue_range?: string;
  target_market?: string;
  description: string;
  website?: string;
}
export interface PartnerApplicationAcknowledgement {
  status: 'submitted';
  received_at: string | null;
}
export interface DeveloperOAuthApplication {
  client_id: string;
  name: string;
  redirect_uris: string[];
  scopes: string[];
  revoked: boolean;
  created_at?: string | null;
  authorization_url?: string;
  token_url?: string;
  pkce_method?: 'S256';
  client_secret_issued?: false;
}
export type DeveloperMachineClientScope = 'catalogue:read' | 'integration:read';
export interface DeveloperMachineClientInput {
  name: string;
  scopes: DeveloperMachineClientScope[];
}
export interface DeveloperMachineClient {
  client_id: string;
  name: string;
  scopes: DeveloperMachineClientScope[];
  revoked: boolean;
  created_at: string | null;
  usage: {
    period_start: string; period_end: string; requests_today: number; requests_last_30_days: number;
    daily_usage: Array<{ date: string; requests: number }>;
  };
  quota: {
    period: string; requests_today: number; requests_this_month: number;
    daily_limit: number | null; daily_remaining: number | null; daily_resets_at: string;
    monthly_limit: number | null; monthly_remaining: number | null; monthly_resets_at: string;
  };
}
export interface DeveloperMachineClientCredentials {
  client_id: string;
  name: string;
  client_secret: string;
  token_url: string;
  scopes: DeveloperMachineClientScope[];
  secret_shown_once: true;
}
export interface BusinessApiResult<T> {
  data: { data: T; meta: { operation: string }; messages: Record<string, string> };
  requestId: string | null;
  replayed: boolean;
}
export class OpesApiError extends Error {
  constructor(status: number, details?: {
    requestId?: string | null; code?: string | null; retryAfter?: number | null; idempotencyKey?: string | null;
  });
  status: number; requestId: string | null; code: string | null;
  retryAfter: number | null; idempotencyKey: string | null;
}
export function createIdempotencyKey(): string;
export class OpesClient {
  constructor(options?: {
    baseUrl?: string; timeoutMs?: number; fetch?: typeof globalThis.fetch;
    oauth?: { clientId: string; clientSecret: string; scopes: Scope[] };
  });
  catalogue(options?: { page?: number; perPage?: number; locale?: Locale }): Promise<CatalogueResponse>;
  integration(): Promise<IntegrationResponse>;
  integrationUsage(): Promise<IntegrationUsageResponse>;
  integrationQuota(): Promise<IntegrationQuotaResponse>;
  serviceOfferings(options?: { locale?: Locale }): Promise<ServiceOfferingsResponse>;
  submitEnquiry(input: EnquiryInput, options: { idempotencyKey: string }): Promise<{
    data: { status: 'recorded'; delivery_status: 'sent' | 'pending'; receipt_delivery_status?: 'sent' | 'pending' };
    idempotencyKey: string; replayed: boolean; requestId: string | null;
  }>;
}
export class OpesBusinessApiClient {
  constructor(options?: {
    baseUrl?: string; timeoutMs?: number; fetch?: typeof globalThis.fetch;
    accessToken?: string; getAccessToken?: () => string | Promise<string>;
  });
  request<T = unknown>(operationId: BusinessOperationId, options?: {
    path?: Record<string, string | number>; query?: Record<string, string | number | boolean>;
    body?: unknown; idempotencyKey?: string; locale?: Locale; signal?: AbortSignal;
  }): Promise<{ data: T; requestId: string | null; replayed: boolean }>;
  listDeveloperApplications(options?: { page?: number; perPage?: number }): Promise<BusinessApiResult<{
    items: DeveloperOAuthApplication[];
    pagination: { current_page: number; per_page: number; total: number; last_page: number };
  }>>;
  registerDeveloperApplication(input: DeveloperOAuthApplicationInput, options: { idempotencyKey: string }): Promise<BusinessApiResult<DeveloperOAuthApplication>>;
  updateDeveloperApplication(clientId: string, input: DeveloperOAuthApplicationInput, options: { idempotencyKey: string }): Promise<BusinessApiResult<DeveloperOAuthApplication>>;
  revokeDeveloperApplication(clientId: string, options: { idempotencyKey: string }): Promise<BusinessApiResult<{ client_id: string; revoked: true }>>;
  listDeveloperMachineClients(options?: { page?: number; perPage?: number }): Promise<BusinessApiResult<{
    items: DeveloperMachineClient[];
    pagination: { current_page: number; per_page: number; total: number; last_page: number };
  }>>;
  registerDeveloperMachineClient(input: DeveloperMachineClientInput, options: { idempotencyKey: string }): Promise<BusinessApiResult<DeveloperMachineClientCredentials>>;
  rotateDeveloperMachineClient(clientId: string, options: { idempotencyKey: string }): Promise<BusinessApiResult<DeveloperMachineClientCredentials>>;
  revokeDeveloperMachineClient(clientId: string, options: { idempotencyKey: string }): Promise<BusinessApiResult<{ client_id: string; revoked: true }>>;
  submitPartnerApplication(input: PartnerApplicationInput, options: { idempotencyKey: string }): Promise<BusinessApiResult<PartnerApplicationAcknowledgement>>;
}
