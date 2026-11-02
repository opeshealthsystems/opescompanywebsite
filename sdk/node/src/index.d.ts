export type Scope = 'catalogue:read' | 'integration:read';
export type Locale = 'en' | 'fr';
export type QuoteOption = 'clinic-saas' | 'clinic-perpetual' | 'hospital-small' | 'hospital-medium-large' | 'specialty-lab' | 'specialty-pharmacy' | 'specialty-radiology' | 'specialty-cardiology' | 'specialty-dental' | 'specialty-dermatology' | 'specialty-pediatrics' | 'specialty-obgyn' | 'care-single-hospital' | 'care-multi-hospital' | 'care-national' | 'health-id-facility' | 'health-id-multi-facility' | 'health-id-national' | 'clinical-cdms-saas' | 'clinical-cdms-perpetual' | 'clinical-triage-saas' | 'clinical-triage-perpetual' | 'clinical-cdss-hospital' | 'clinical-cdss-network' | 'government-insurance' | 'government-public-health' | 'government-national-platform' | 'service-implementation' | 'service-data-migration' | 'service-training' | 'service-custom-integration' | 'maintenance-standard' | 'maintenance-premium' | 'maintenance-enterprise' | 'managed-hosting' | 'custom-proposal';
export type { BusinessOperationId } from './business-operations.js';
import type { BusinessOperationId } from './business-operations.js';
export interface EnquiryInput {
  name: string; email: string; phone?: string | null; facility_type?: string | null;
  products?: string | null; message?: string | null; product_slug?: string | null; locale?: Locale; quote_option?: QuoteOption;
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
}
