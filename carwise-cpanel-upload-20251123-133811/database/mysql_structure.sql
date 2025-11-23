CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "personal_access_tokens"(
  "id" integer primary key autoincrement not null,
  "tokenable_type" varchar not null,
  "tokenable_id" integer not null,
  "name" text not null,
  "token" varchar not null,
  "abilities" text,
  "last_used_at" datetime,
  "expires_at" datetime,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "personal_access_tokens_tokenable_type_tokenable_id_index" on "personal_access_tokens"(
  "tokenable_type",
  "tokenable_id"
);
CREATE UNIQUE INDEX "personal_access_tokens_token_unique" on "personal_access_tokens"(
  "token"
);
CREATE INDEX "personal_access_tokens_expires_at_index" on "personal_access_tokens"(
  "expires_at"
);
CREATE TABLE IF NOT EXISTS "cars"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "brand" varchar not null,
  "model" varchar not null,
  "year" integer not null,
  "vin" varchar,
  "color" varchar,
  "license_plate" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "fuel_type" varchar,
  "transmission" varchar,
  "mileage" integer,
  "purchase_date" date,
  "purchase_price" numeric,
  "notes" text,
  "specifications" text,
  "maintenance_history" text,
  "status" varchar not null default 'active',
  "current_mileage" integer,
  "last_service_date" date,
  "last_service_mileage" integer,
  "last_oil_change_date" date,
  "last_oil_change_mileage" integer,
  "oil_change_interval" integer not null default '15000',
  "last_tire_change_date" date,
  "last_tire_change_mileage" integer,
  "tire_change_interval" integer not null default '50000',
  "last_timing_belt_change_date" date,
  "last_timing_belt_change_mileage" integer,
  "timing_belt_change_interval" integer not null default '100000',
  "last_brake_pad_change_date" date,
  "last_brake_pad_change_mileage" integer,
  "brake_pad_change_interval" integer not null default '60000',
  "last_air_filter_change_date" date,
  "last_air_filter_change_mileage" integer,
  "air_filter_change_interval" integer not null default '30000',
  "last_fuel_filter_change_date" date,
  "last_fuel_filter_change_mileage" integer,
  "fuel_filter_change_interval" integer not null default '40000',
  "last_spark_plugs_change_date" date,
  "last_spark_plugs_change_mileage" integer,
  "spark_plugs_change_interval" integer not null default '60000',
  "battery_installation_date" date,
  "battery_life_years" integer not null default '4',
  "current_tire_type" varchar check("current_tire_type" in('summer', 'winter', 'all_season')) not null default 'all_season',
  "last_seasonal_tire_change_date" date,
  "insurance_expiry_date" date,
  "registration_expiry_date" date,
  "maintenance_notifications_enabled" tinyint(1) not null default '1',
  "notification_advance_days" integer not null default '30',
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "diagnoses"(
  "id" integer primary key autoincrement not null,
  "car_id" integer not null,
  "user_id" integer not null,
  "media_file" varchar,
  "media_type" varchar check("media_type" in('image', 'video', 'audio')),
  "description" text,
  "ai_analysis" text,
  "problem" varchar,
  "confidence" integer,
  "solutions" text,
  "next_steps" text,
  "status" varchar check("status" in('pending', 'analyzing', 'completed', 'failed')) not null default 'pending',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("car_id") references "cars"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "mechanics"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "experience_years" integer not null,
  "expertise" text not null,
  "location" varchar not null,
  "hourly_rate" numeric not null,
  "rating" numeric not null default '0',
  "review_count" integer not null default '0',
  "availability" varchar check("availability" in('available', 'busy', 'offline')) not null default 'available',
  "bio" text,
  "certifications" text,
  "is_verified" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "name" varchar,
  "phone" varchar,
  "email" varchar,
  "address" varchar,
  "city" varchar,
  "country" varchar,
  "lat" numeric,
  "lng" numeric,
  "geohash" varchar,
  "services" text,
  "logo_path" varchar,
  "gallery" text,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "diagnosis_media"(
  "id" integer primary key autoincrement not null,
  "diagnosis_session_id" integer not null,
  "file_name" varchar not null,
  "file_path" varchar not null,
  "file_type" varchar not null,
  "file_size" integer not null,
  "mime_type" varchar not null,
  "metadata" text,
  "ai_analysis" varchar,
  "ai_tags" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("diagnosis_session_id") references "diagnosis_sessions"("id") on delete cascade
);
CREATE INDEX "diagnosis_media_diagnosis_session_id_index" on "diagnosis_media"(
  "diagnosis_session_id"
);
CREATE INDEX "diagnosis_media_file_type_index" on "diagnosis_media"(
  "file_type"
);
CREATE TABLE IF NOT EXISTS "diagnosis_results"(
  "id" integer primary key autoincrement not null,
  "diagnosis_session_id" integer not null,
  "problem_title" varchar not null,
  "problem_description" text not null,
  "severity" varchar check("severity" in('low', 'medium', 'high', 'critical')) not null,
  "confidence_score" integer not null,
  "likely_causes" text not null,
  "recommended_actions" text not null,
  "estimated_costs" text,
  "ai_insights" text,
  "related_issues" text,
  "requires_immediate_attention" tinyint(1) not null default '0',
  "ai_model_version" varchar,
  "analysis_completed_at" datetime not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("diagnosis_session_id") references "diagnosis_sessions"("id") on delete cascade
);
CREATE INDEX "diagnosis_results_diagnosis_session_id_index" on "diagnosis_results"(
  "diagnosis_session_id"
);
CREATE INDEX "diagnosis_results_severity_index" on "diagnosis_results"(
  "severity"
);
CREATE INDEX "diagnosis_results_confidence_score_index" on "diagnosis_results"(
  "confidence_score"
);
CREATE INDEX "diagnosis_results_requires_immediate_attention_index" on "diagnosis_results"(
  "requires_immediate_attention"
);
CREATE TABLE IF NOT EXISTS "user_sessions"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "session_id" varchar not null,
  "ip_address" varchar not null,
  "user_agent" text not null,
  "device_type" varchar,
  "browser" varchar,
  "operating_system" varchar,
  "country" varchar,
  "city" varchar,
  "latitude" numeric,
  "longitude" numeric,
  "is_active" tinyint(1) not null default '1',
  "last_activity_at" datetime not null,
  "expires_at" datetime not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "user_sessions_user_id_is_active_index" on "user_sessions"(
  "user_id",
  "is_active"
);
CREATE INDEX "user_sessions_session_id_index" on "user_sessions"("session_id");
CREATE INDEX "user_sessions_last_activity_at_index" on "user_sessions"(
  "last_activity_at"
);
CREATE INDEX "user_sessions_expires_at_index" on "user_sessions"("expires_at");
CREATE UNIQUE INDEX "user_sessions_session_id_unique" on "user_sessions"(
  "session_id"
);
CREATE TABLE IF NOT EXISTS "user_activity_logs"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "session_id" varchar,
  "activity_type" varchar not null,
  "activity_description" varchar not null,
  "ip_address" varchar not null,
  "user_agent" text,
  "url" varchar,
  "method" varchar,
  "request_data" text,
  "response_data" text,
  "response_status" integer,
  "execution_time_ms" integer,
  "device_type" varchar,
  "browser" varchar,
  "operating_system" varchar,
  "country" varchar,
  "city" varchar,
  "metadata" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "user_activity_logs_user_id_activity_type_index" on "user_activity_logs"(
  "user_id",
  "activity_type"
);
CREATE INDEX "user_activity_logs_session_id_index" on "user_activity_logs"(
  "session_id"
);
CREATE INDEX "user_activity_logs_activity_type_index" on "user_activity_logs"(
  "activity_type"
);
CREATE INDEX "user_activity_logs_created_at_index" on "user_activity_logs"(
  "created_at"
);
CREATE INDEX "user_activity_logs_ip_address_index" on "user_activity_logs"(
  "ip_address"
);
CREATE TABLE IF NOT EXISTS "user_preferences"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "preference_key" varchar not null,
  "preference_value" text not null,
  "preference_type" varchar not null default 'string',
  "description" text,
  "is_public" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "user_preferences_user_id_preference_key_unique" on "user_preferences"(
  "user_id",
  "preference_key"
);
CREATE INDEX "user_preferences_user_id_index" on "user_preferences"("user_id");
CREATE INDEX "user_preferences_preference_key_index" on "user_preferences"(
  "preference_key"
);
CREATE INDEX "user_preferences_preference_type_index" on "user_preferences"(
  "preference_type"
);
CREATE INDEX "cars_user_id_status_index" on "cars"("user_id", "status");
CREATE INDEX "cars_brand_model_index" on "cars"("brand", "model");
CREATE INDEX "cars_year_index" on "cars"("year");
CREATE TABLE IF NOT EXISTS "car_brands"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "country" varchar not null,
  "logo_url" varchar,
  "website" varchar,
  "description" text,
  "founded_year" integer,
  "headquarters" varchar,
  "specialties" text,
  "is_active" tinyint(1) not null default '1',
  "is_popular" tinyint(1) not null default '0',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "car_brands_is_active_is_popular_index" on "car_brands"(
  "is_active",
  "is_popular"
);
CREATE INDEX "car_brands_country_index" on "car_brands"("country");
CREATE INDEX "car_brands_sort_order_index" on "car_brands"("sort_order");
CREATE UNIQUE INDEX "car_brands_slug_unique" on "car_brands"("slug");
CREATE TABLE IF NOT EXISTS "car_models"(
  "id" integer primary key autoincrement not null,
  "car_brand_id" integer not null,
  "name" varchar not null,
  "slug" varchar not null,
  "generation" varchar,
  "start_year" integer,
  "end_year" integer,
  "body_type" varchar,
  "segment" varchar,
  "engine_options" text,
  "transmission_options" text,
  "fuel_types" text,
  "specifications" text,
  "description" text,
  "image_url" varchar,
  "is_active" tinyint(1) not null default '1',
  "is_popular" tinyint(1) not null default '0',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("car_brand_id") references "car_brands"("id") on delete cascade
);
CREATE INDEX "car_models_car_brand_id_is_active_index" on "car_models"(
  "car_brand_id",
  "is_active"
);
CREATE INDEX "car_models_start_year_end_year_index" on "car_models"(
  "start_year",
  "end_year"
);
CREATE INDEX "car_models_body_type_index" on "car_models"("body_type");
CREATE INDEX "car_models_segment_index" on "car_models"("segment");
CREATE INDEX "car_models_is_popular_index" on "car_models"("is_popular");
CREATE INDEX "car_models_sort_order_index" on "car_models"("sort_order");
CREATE UNIQUE INDEX "unique_brand_model_generation" on "car_models"(
  "car_brand_id",
  "name",
  "generation"
);
CREATE TABLE IF NOT EXISTS "currencies"(
  "id" integer primary key autoincrement not null,
  "code" varchar not null,
  "name" varchar not null,
  "symbol" varchar not null,
  "country" varchar not null,
  "exchange_rate" numeric not null default '1',
  "is_active" tinyint(1) not null default '1',
  "is_default" tinyint(1) not null default '0',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "currencies_is_active_sort_order_index" on "currencies"(
  "is_active",
  "sort_order"
);
CREATE UNIQUE INDEX "currencies_code_unique" on "currencies"("code");
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "password" varchar not null,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "phone" varchar,
  "role" varchar check("role" in('customer', 'mechanic', 'admin')) not null default 'customer',
  "phone_verified_at" datetime,
  "notification_preferences" text,
  "privacy_settings" text,
  "preferred_currency_id" integer,
  "first_name" varchar,
  "last_name" varchar,
  "date_of_birth" date,
  "gender" varchar check("gender" in('male', 'female', 'other', 'prefer_not_to_say')),
  "avatar" varchar,
  "bio" text,
  "location" varchar,
  "timezone" varchar not null default 'UTC',
  "language" varchar not null default 'en',
  "status" varchar check("status" in('active', 'inactive', 'suspended', 'pending')) not null default 'active',
  "last_login_at" datetime,
  "last_login_ip" varchar,
  "login_history" text,
  foreign key("preferred_currency_id") references currencies("id") on delete set null on update no action
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE INDEX "mechanics_city_country_index" on "mechanics"("city", "country");
CREATE INDEX "mechanics_geohash_index" on "mechanics"("geohash");
CREATE TABLE IF NOT EXISTS "mechanic_reviews"(
  "id" integer primary key autoincrement not null,
  "mechanic_id" integer not null,
  "user_id" integer not null,
  "diagnosis_session_id" integer,
  "review_text" text not null,
  "rating" integer not null,
  "rating_breakdown" text,
  "service_type" varchar,
  "service_cost" numeric,
  "service_date" date,
  "photos" text,
  "status" varchar check("status" in('pending', 'approved', 'rejected', 'flagged')) not null default 'pending',
  "admin_notes" text,
  "approved_at" datetime,
  "approved_by" integer,
  "helpful_count" integer not null default '0',
  "not_helpful_count" integer not null default '0',
  "mechanic_response" text,
  "mechanic_response_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("mechanic_id") references "mechanics"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("diagnosis_session_id") references "diagnosis_sessions"("id") on delete set null,
  foreign key("approved_by") references "users"("id") on delete set null
);
CREATE INDEX "mechanic_reviews_mechanic_id_status_index" on "mechanic_reviews"(
  "mechanic_id",
  "status"
);
CREATE INDEX "mechanic_reviews_user_id_created_at_index" on "mechanic_reviews"(
  "user_id",
  "created_at"
);
CREATE INDEX "mechanic_reviews_rating_status_index" on "mechanic_reviews"(
  "rating",
  "status"
);
CREATE INDEX "mechanic_reviews_service_date_index" on "mechanic_reviews"(
  "service_date"
);
CREATE INDEX "mechanic_reviews_approved_at_index" on "mechanic_reviews"(
  "approved_at"
);
CREATE UNIQUE INDEX "unique_mechanic_user_session_review" on "mechanic_reviews"(
  "mechanic_id",
  "user_id",
  "diagnosis_session_id"
);
CREATE TABLE IF NOT EXISTS "review_helpfulness"(
  "id" integer primary key autoincrement not null,
  "review_id" integer not null,
  "user_id" integer not null,
  "is_helpful" tinyint(1) not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("review_id") references "mechanic_reviews"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "review_helpfulness_review_id_user_id_index" on "review_helpfulness"(
  "review_id",
  "user_id"
);
CREATE INDEX "review_helpfulness_user_id_created_at_index" on "review_helpfulness"(
  "user_id",
  "created_at"
);
CREATE UNIQUE INDEX "unique_review_user_helpfulness" on "review_helpfulness"(
  "review_id",
  "user_id"
);
CREATE TABLE IF NOT EXISTS "notifications"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "type" varchar not null,
  "title" varchar not null,
  "message" text not null,
  "data" text,
  "action_url" varchar,
  "action_text" varchar,
  "is_read" tinyint(1) not null default '0',
  "read_at" datetime,
  "priority" varchar check("priority" in('low', 'normal', 'high', 'urgent')) not null default 'normal',
  "in_app" tinyint(1) not null default '1',
  "email" tinyint(1) not null default '0',
  "push" tinyint(1) not null default '0',
  "sms" tinyint(1) not null default '0',
  "sent_at" datetime,
  "delivered_at" datetime,
  "failed_at" datetime,
  "failure_reason" text,
  "expires_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "notifications_user_id_is_read_index" on "notifications"(
  "user_id",
  "is_read"
);
CREATE INDEX "notifications_user_id_created_at_index" on "notifications"(
  "user_id",
  "created_at"
);
CREATE INDEX "notifications_type_created_at_index" on "notifications"(
  "type",
  "created_at"
);
CREATE INDEX "notifications_priority_created_at_index" on "notifications"(
  "priority",
  "created_at"
);
CREATE INDEX "notifications_expires_at_index" on "notifications"("expires_at");
CREATE TABLE IF NOT EXISTS "payments"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "payment_id" varchar not null,
  "provider" varchar not null,
  "type" varchar not null,
  "status" varchar not null,
  "amount" numeric not null,
  "currency" varchar not null default 'USD',
  "description" varchar not null,
  "metadata" text,
  "related_type" varchar,
  "related_id" integer,
  "payment_method" varchar,
  "payment_method_id" varchar,
  "payment_method_details" text,
  "provider_data" text,
  "provider_transaction_id" varchar,
  "provider_fee_id" varchar,
  "refunded_amount" numeric not null default '0',
  "refund_data" text,
  "refunded_at" datetime,
  "paid_at" datetime,
  "failed_at" datetime,
  "cancelled_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "payments_user_id_status_index" on "payments"(
  "user_id",
  "status"
);
CREATE INDEX "payments_provider_payment_id_index" on "payments"(
  "provider",
  "payment_id"
);
CREATE INDEX "payments_type_status_index" on "payments"("type", "status");
CREATE INDEX "payments_related_type_related_id_index" on "payments"(
  "related_type",
  "related_id"
);
CREATE INDEX "payments_created_at_index" on "payments"("created_at");
CREATE INDEX "payments_paid_at_index" on "payments"("paid_at");
CREATE UNIQUE INDEX "payments_payment_id_unique" on "payments"("payment_id");
CREATE TABLE IF NOT EXISTS "subscriptions"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "subscription_id" varchar not null,
  "provider" varchar not null,
  "plan_name" varchar not null,
  "status" varchar not null,
  "amount" numeric not null,
  "currency" varchar not null default 'USD',
  "interval" varchar not null,
  "interval_count" integer not null default '1',
  "current_period_start" datetime not null,
  "current_period_end" datetime not null,
  "trial_start" datetime,
  "trial_end" datetime,
  "canceled_at" datetime,
  "ended_at" datetime,
  "features" text,
  "limits" text,
  "provider_data" text,
  "provider_customer_id" varchar,
  "payment_method_id" varchar,
  "payment_method_details" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "subscriptions_user_id_status_index" on "subscriptions"(
  "user_id",
  "status"
);
CREATE INDEX "subscriptions_provider_subscription_id_index" on "subscriptions"(
  "provider",
  "subscription_id"
);
CREATE INDEX "subscriptions_plan_name_status_index" on "subscriptions"(
  "plan_name",
  "status"
);
CREATE INDEX "subscriptions_current_period_end_index" on "subscriptions"(
  "current_period_end"
);
CREATE INDEX "subscriptions_trial_end_index" on "subscriptions"("trial_end");
CREATE UNIQUE INDEX "subscriptions_subscription_id_unique" on "subscriptions"(
  "subscription_id"
);
CREATE TABLE IF NOT EXISTS "chat_conversations"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "mechanic_id" integer not null,
  "subject" varchar,
  "status" varchar check("status" in('active', 'closed', 'archived')) not null default 'active',
  "priority" varchar check("priority" in('low', 'normal', 'high', 'urgent')) not null default 'normal',
  "related_type" varchar,
  "related_id" integer,
  "last_message_at" datetime,
  "closed_at" datetime,
  "archived_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("mechanic_id") references "mechanics"("id") on delete cascade
);
CREATE INDEX "chat_conversations_user_id_status_index" on "chat_conversations"(
  "user_id",
  "status"
);
CREATE INDEX "chat_conversations_mechanic_id_status_index" on "chat_conversations"(
  "mechanic_id",
  "status"
);
CREATE INDEX "chat_conversations_status_last_message_at_index" on "chat_conversations"(
  "status",
  "last_message_at"
);
CREATE INDEX "chat_conversations_related_type_related_id_index" on "chat_conversations"(
  "related_type",
  "related_id"
);
CREATE TABLE IF NOT EXISTS "chat_messages"(
  "id" integer primary key autoincrement not null,
  "conversation_id" integer not null,
  "sender_id" integer not null,
  "sender_type" varchar check("sender_type" in('user', 'mechanic')) not null,
  "message" text not null,
  "message_type" varchar check("message_type" in('text', 'image', 'file', 'diagnosis', 'appointment')) not null default 'text',
  "attachment_path" varchar,
  "attachment_name" varchar,
  "attachment_type" varchar,
  "attachment_size" integer,
  "is_read" tinyint(1) not null default '0',
  "read_at" datetime,
  "is_edited" tinyint(1) not null default '0',
  "edited_at" datetime,
  "is_deleted" tinyint(1) not null default '0',
  "deleted_at" datetime,
  "reply_to_id" integer,
  "metadata" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("conversation_id") references "chat_conversations"("id") on delete cascade,
  foreign key("sender_id") references "users"("id") on delete cascade,
  foreign key("reply_to_id") references "chat_messages"("id") on delete set null
);
CREATE INDEX "chat_messages_conversation_id_created_at_index" on "chat_messages"(
  "conversation_id",
  "created_at"
);
CREATE INDEX "chat_messages_sender_id_sender_type_index" on "chat_messages"(
  "sender_id",
  "sender_type"
);
CREATE INDEX "chat_messages_is_read_created_at_index" on "chat_messages"(
  "is_read",
  "created_at"
);
CREATE INDEX "chat_messages_message_type_index" on "chat_messages"(
  "message_type"
);
CREATE TABLE IF NOT EXISTS "appointments"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "mechanic_id" integer not null,
  "appointment_number" varchar not null,
  "service_type" varchar not null,
  "description" text not null,
  "priority" varchar check("priority" in('low', 'normal', 'high', 'urgent')) not null default 'normal',
  "status" varchar check("status" in('pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show')) not null default 'pending',
  "scheduled_at" datetime not null,
  "estimated_duration" datetime not null,
  "actual_start_at" datetime,
  "actual_end_at" datetime,
  "location_type" varchar check("location_type" in('mechanic_shop', 'customer_location', 'mobile_service')) not null default 'mechanic_shop',
  "address" text,
  "latitude" numeric,
  "longitude" numeric,
  "vehicle_make" varchar not null,
  "vehicle_model" varchar not null,
  "vehicle_year" integer not null,
  "vehicle_vin" varchar,
  "vehicle_license_plate" varchar,
  "vehicle_mileage" integer,
  "estimated_cost" numeric,
  "actual_cost" numeric,
  "currency" varchar not null default 'USD',
  "cost_breakdown" text,
  "related_type" varchar,
  "related_id" integer,
  "notes" text,
  "customer_notes" text,
  "mechanic_notes" text,
  "requires_follow_up" tinyint(1) not null default '0',
  "follow_up_date" datetime,
  "follow_up_notes" text,
  "cancelled_at" datetime,
  "cancellation_reason" varchar,
  "cancelled_by" varchar check("cancelled_by" in('customer', 'mechanic', 'system')),
  "customer_rating" integer,
  "customer_feedback" text,
  "mechanic_rating" integer,
  "mechanic_feedback" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("mechanic_id") references "mechanics"("id") on delete cascade
);
CREATE INDEX "appointments_user_id_status_index" on "appointments"(
  "user_id",
  "status"
);
CREATE INDEX "appointments_mechanic_id_status_index" on "appointments"(
  "mechanic_id",
  "status"
);
CREATE INDEX "appointments_scheduled_at_index" on "appointments"(
  "scheduled_at"
);
CREATE INDEX "appointments_status_scheduled_at_index" on "appointments"(
  "status",
  "scheduled_at"
);
CREATE INDEX "appointments_service_type_status_index" on "appointments"(
  "service_type",
  "status"
);
CREATE INDEX "appointments_related_type_related_id_index" on "appointments"(
  "related_type",
  "related_id"
);
CREATE INDEX "appointments_appointment_number_index" on "appointments"(
  "appointment_number"
);
CREATE UNIQUE INDEX "appointments_appointment_number_unique" on "appointments"(
  "appointment_number"
);
CREATE TABLE IF NOT EXISTS "car_maintenance_history"(
  "id" integer primary key autoincrement not null,
  "car_id" integer not null,
  "user_id" integer not null,
  "maintenance_type" varchar not null,
  "title" varchar not null,
  "description" text,
  "service_date" date not null,
  "service_mileage" integer not null,
  "cost" numeric,
  "currency" varchar not null default 'USD',
  "service_provider" varchar,
  "service_provider_contact" varchar,
  "service_provider_address" text,
  "parts_used" text,
  "materials_used" text,
  "next_service_due_date" date,
  "next_service_due_mileage" integer,
  "attachments" text,
  "notes" text,
  "status" varchar check("status" in('completed', 'scheduled', 'cancelled')) not null default 'completed',
  "is_warranty_work" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("car_id") references "cars"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "car_maintenance_history_car_id_service_date_index" on "car_maintenance_history"(
  "car_id",
  "service_date"
);
CREATE INDEX "car_maintenance_history_maintenance_type_service_date_index" on "car_maintenance_history"(
  "maintenance_type",
  "service_date"
);
CREATE INDEX "car_maintenance_history_user_id_service_date_index" on "car_maintenance_history"(
  "user_id",
  "service_date"
);
CREATE TABLE IF NOT EXISTS "maintenance_notifications"(
  "id" integer primary key autoincrement not null,
  "car_id" integer not null,
  "user_id" integer not null,
  "maintenance_type" varchar not null,
  "title" varchar not null,
  "message" text not null,
  "priority" varchar check("priority" in('low', 'normal', 'high', 'urgent')) not null default 'normal',
  "due_date" date not null,
  "due_mileage" integer,
  "current_mileage" integer,
  "is_read" tinyint(1) not null default '0',
  "read_at" datetime,
  "is_sent" tinyint(1) not null default '0',
  "sent_at" datetime,
  "in_app" tinyint(1) not null default '1',
  "email" tinyint(1) not null default '0',
  "push" tinyint(1) not null default '0',
  "sms" tinyint(1) not null default '0',
  "action_taken" tinyint(1) not null default '0',
  "action_taken_at" datetime,
  "action_notes" text,
  "is_recurring" tinyint(1) not null default '0',
  "recurring_interval_days" integer,
  "next_notification_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("car_id") references "cars"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "maintenance_notifications_car_id_due_date_index" on "maintenance_notifications"(
  "car_id",
  "due_date"
);
CREATE INDEX "maintenance_notifications_user_id_is_read_index" on "maintenance_notifications"(
  "user_id",
  "is_read"
);
CREATE INDEX "maintenance_notifications_maintenance_type_due_date_index" on "maintenance_notifications"(
  "maintenance_type",
  "due_date"
);
CREATE INDEX "maintenance_notifications_priority_due_date_index" on "maintenance_notifications"(
  "priority",
  "due_date"
);
CREATE TABLE IF NOT EXISTS "car_images"(
  "id" integer primary key autoincrement not null,
  "brand" varchar not null,
  "model" varchar not null,
  "year" integer,
  "body_type" varchar,
  "color" varchar,
  "image_url" varchar not null,
  "thumbnail_url" varchar,
  "image_type" varchar not null default 'exterior',
  "angle" varchar not null default 'front',
  "width" integer,
  "height" integer,
  "source" varchar,
  "is_primary" tinyint(1) not null default '0',
  "is_active" tinyint(1) not null default '1',
  "metadata" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "car_images_brand_model_index" on "car_images"("brand", "model");
CREATE INDEX "car_images_brand_model_year_index" on "car_images"(
  "brand",
  "model",
  "year"
);
CREATE INDEX "car_images_image_type_angle_index" on "car_images"(
  "image_type",
  "angle"
);
CREATE INDEX "car_images_is_primary_index" on "car_images"("is_primary");
CREATE INDEX "car_images_is_active_index" on "car_images"("is_active");
CREATE INDEX "users_email_index" on "users"("email");
CREATE INDEX "users_role_index" on "users"("role");
CREATE INDEX "users_email_verified_at_index" on "users"("email_verified_at");
CREATE INDEX "users_created_at_index" on "users"("created_at");
CREATE INDEX "cars_user_id_index" on "cars"("user_id");
CREATE INDEX "cars_user_id_is_primary_index" on "cars"(
  "user_id",
  "is_primary"
);
CREATE INDEX "cars_brand_id_index" on "cars"("brand_id");
CREATE INDEX "cars_model_id_index" on "cars"("model_id");
CREATE INDEX "cars_brand_id_model_id_index" on "cars"("brand_id", "model_id");
CREATE TABLE IF NOT EXISTS "diagnosis_sessions"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "session_id" varchar not null,
  "make" varchar not null,
  "model" varchar not null,
  "year" integer not null,
  "mileage" integer,
  "engine_type" varchar,
  "engine_size" varchar,
  "description" text not null,
  "symptoms" text,
  "status" varchar not null default('pending'),
  "ai_response" text,
  "confidence_score" integer,
  "severity" varchar,
  "processed_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "car_id" integer,
  foreign key("user_id") references users("id") on delete cascade on update no action
);
CREATE INDEX "diagnosis_sessions_created_at_index" on "diagnosis_sessions"(
  "created_at"
);
CREATE INDEX "diagnosis_sessions_session_id_index" on "diagnosis_sessions"(
  "session_id"
);
CREATE UNIQUE INDEX "diagnosis_sessions_session_id_unique" on "diagnosis_sessions"(
  "session_id"
);
CREATE INDEX "diagnosis_sessions_user_id_status_index" on "diagnosis_sessions"(
  "user_id",
  "status"
);
CREATE INDEX "cars_license_plate_index" on "cars"("license_plate");
CREATE INDEX "diagnosis_sessions_status_index" on "diagnosis_sessions"(
  "status"
);
CREATE INDEX "diagnosis_sessions_car_id_index" on "diagnosis_sessions"(
  "car_id"
);
CREATE INDEX "diagnosis_sessions_processed_at_index" on "diagnosis_sessions"(
  "processed_at"
);
CREATE INDEX "diagnosis_sessions_severity_index" on "diagnosis_sessions"(
  "severity"
);
CREATE INDEX "mechanics_user_id_index" on "mechanics"("user_id");
CREATE INDEX "mechanics_is_verified_index" on "mechanics"("is_verified");
CREATE INDEX "mechanics_is_available_index" on "mechanics"("is_available");
CREATE INDEX "mechanics_city_index" on "mechanics"("city");
CREATE INDEX "mechanics_rating_index" on "mechanics"("rating");
CREATE INDEX "mechanic_reviews_mechanic_id_index" on "mechanic_reviews"(
  "mechanic_id"
);
CREATE INDEX "mechanic_reviews_user_id_index" on "mechanic_reviews"("user_id");
CREATE INDEX "mechanic_reviews_rating_index" on "mechanic_reviews"("rating");
CREATE INDEX "notifications_user_id_index" on "notifications"("user_id");
CREATE INDEX "notifications_read_at_index" on "notifications"("read_at");
CREATE INDEX "notifications_type_index" on "notifications"("type");
CREATE INDEX "appointments_user_id_index" on "appointments"("user_id");
CREATE INDEX "appointments_mechanic_id_index" on "appointments"("mechanic_id");
CREATE INDEX "appointments_car_id_index" on "appointments"("car_id");
CREATE INDEX "appointments_status_index" on "appointments"("status");
CREATE INDEX "payments_user_id_index" on "payments"("user_id");
CREATE INDEX "payments_appointment_id_index" on "payments"("appointment_id");
CREATE INDEX "payments_status_index" on "payments"("status");
CREATE INDEX "cars_user_id_created_at_index" on "cars"(
  "user_id",
  "created_at"
);
CREATE INDEX "car_brands_is_popular_index" on "car_brands"("is_popular");
CREATE INDEX "car_brands_is_popular_name_index" on "car_brands"(
  "is_popular",
  "name"
);
CREATE INDEX "car_models_brand_id_index" on "car_models"("brand_id");
CREATE INDEX "car_models_brand_id_name_index" on "car_models"(
  "brand_id",
  "name"
);
CREATE INDEX "car_models_is_active_index" on "car_models"("is_active");
CREATE INDEX "diagnosis_sessions_car_id_created_at_index" on "diagnosis_sessions"(
  "car_id",
  "created_at"
);
CREATE TABLE IF NOT EXISTS "diagnosis_suggested_parts"(
  "id" integer primary key autoincrement not null,
  "diagnosis_result_id" integer not null,
  "car_part_id" integer not null,
  "suggestion_reason" varchar not null,
  "priority" integer not null default '1',
  "relevance_score" numeric not null default '0',
  "is_required" tinyint(1) not null default '0',
  "is_recommended" tinyint(1) not null default '1',
  "is_alternative" tinyint(1) not null default '0',
  "quantity_needed" integer not null default '1',
  "usage_notes" varchar,
  "estimated_cost" numeric,
  "cost_currency" varchar not null default 'USD',
  "cost_breakdown" text,
  "estimated_installation_time" integer,
  "installation_difficulty" varchar not null default 'medium',
  "installation_notes" text,
  "user_selected" tinyint(1) not null default '0',
  "user_purchased" tinyint(1) not null default '0',
  "purchased_at" datetime,
  "user_notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("diagnosis_result_id") references "diagnosis_results"("id") on delete cascade,
  foreign key("car_part_id") references "car_parts"("id") on delete cascade
);
CREATE INDEX "diagnosis_suggested_parts_diagnosis_result_id_priority_index" on "diagnosis_suggested_parts"(
  "diagnosis_result_id",
  "priority"
);
CREATE INDEX "diagnosis_suggested_parts_car_part_id_is_required_index" on "diagnosis_suggested_parts"(
  "car_part_id",
  "is_required"
);
CREATE INDEX "diagnosis_suggested_parts_relevance_score_is_recommended_index" on "diagnosis_suggested_parts"(
  "relevance_score",
  "is_recommended"
);
CREATE UNIQUE INDEX "diagnosis_suggested_parts_diagnosis_result_id_car_part_id_unique" on "diagnosis_suggested_parts"(
  "diagnosis_result_id",
  "car_part_id"
);
CREATE TABLE IF NOT EXISTS "authorized_companies"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "description" text,
  "logo_url" varchar,
  "website" varchar,
  "email" varchar,
  "phone" varchar,
  "address" varchar,
  "city" varchar,
  "country" varchar,
  "postal_code" varchar,
  "languages_supported" text,
  "currencies_supported" text,
  "countries_served" text,
  "specializations" text,
  "brands_authorized" text,
  "certification_body" varchar,
  "certification_number" varchar,
  "certification_date" date,
  "certification_expiry" date,
  "is_verified" tinyint(1) not null default '0',
  "is_active" tinyint(1) not null default '1',
  "is_featured" tinyint(1) not null default '0',
  "sort_order" integer not null default '0',
  "rating" numeric,
  "review_count" integer not null default '0',
  "parts_count" integer not null default '0',
  "orders_count" integer not null default '0',
  "total_sales" numeric not null default '0',
  "payment_methods" varchar,
  "shipping_methods" varchar,
  "shipping_time_days" integer,
  "shipping_cost_base" numeric,
  "shipping_cost_currency" varchar not null default 'USD',
  "offers_warranty" tinyint(1) not null default '1',
  "warranty_months" integer,
  "offers_installation" tinyint(1) not null default '0',
  "installation_cost_base" numeric,
  "installation_cost_currency" varchar not null default 'USD',
  "return_policy" text,
  "terms_conditions" text,
  "social_media" text,
  "contact_hours" text,
  "timezone" varchar not null default 'UTC',
  "last_activity" datetime,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "authorized_companies_slug_unique" on "authorized_companies"(
  "slug"
);
CREATE TABLE IF NOT EXISTS "car_parts"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "part_number" varchar not null,
  "description" text not null,
  "category" varchar not null,
  "subcategory" varchar,
  "compatible_brands" text,
  "compatible_models" text,
  "compatible_years" text,
  "engine_type" varchar,
  "engine_size" varchar,
  "manufacturer" varchar not null,
  "oem_number" varchar,
  "aftermarket_brand" varchar,
  "aftermarket_number" varchar,
  "price" numeric not null,
  "currency" varchar not null default('USD'),
  "stock_quantity" integer not null default('0'),
  "in_stock" tinyint(1) not null default('1'),
  "availability_status" varchar not null default('available'),
  "quality_grade" varchar not null default('standard'),
  "is_oem" tinyint(1) not null default('0'),
  "is_certified" tinyint(1) not null default('0'),
  "certifications" text,
  "weight" varchar,
  "dimensions" varchar,
  "material" varchar,
  "color" varchar,
  "installation_time_minutes" integer,
  "difficulty_level" varchar not null default('medium'),
  "installation_notes" text,
  "warranty_months" integer not null default('12'),
  "image_url" varchar,
  "additional_images" text,
  "manual_url" varchar,
  "datasheet_url" varchar,
  "slug" varchar not null,
  "search_keywords" text,
  "meta_description" text,
  "is_active" tinyint(1) not null default('1'),
  "is_featured" tinyint(1) not null default('0'),
  "sort_order" integer not null default('0'),
  "featured_until" datetime,
  "supplier_name" varchar,
  "supplier_contact" varchar,
  "supplier_website" varchar,
  "view_count" integer not null default('0'),
  "purchase_count" integer not null default('0'),
  "rating" numeric not null default('0'),
  "review_count" integer not null default('0'),
  "created_at" datetime,
  "updated_at" datetime,
  "authorized_company_id" integer,
  "company_part_number" varchar,
  "international_pricing" text,
  "shipping_info" text,
  "warranty_details" text,
  "is_international_shipping" tinyint(1) not null default '0',
  "available_countries" text,
  "discount_percentage" numeric,
  "discount_valid_until" datetime,
  "is_bulk_available" tinyint(1) not null default '0',
  "bulk_minimum_quantity" integer,
  "bulk_discount_percentage" numeric,
  "modification_notes" text,
  "installation_guides" text,
  "compatibility_matrix" text,
  foreign key("authorized_company_id") references "authorized_companies"("id") on delete set null
);
CREATE INDEX "car_parts_category_is_active_index" on "car_parts"(
  "category",
  "is_active"
);
CREATE INDEX "car_parts_in_stock_is_active_index" on "car_parts"(
  "in_stock",
  "is_active"
);
CREATE INDEX "car_parts_is_featured_is_active_index" on "car_parts"(
  "is_featured",
  "is_active"
);
CREATE INDEX "car_parts_manufacturer_is_active_index" on "car_parts"(
  "manufacturer",
  "is_active"
);
CREATE UNIQUE INDEX "car_parts_part_number_unique" on "car_parts"(
  "part_number"
);
CREATE INDEX "car_parts_price_is_active_index" on "car_parts"(
  "price",
  "is_active"
);
CREATE INDEX "car_parts_quality_grade_is_active_index" on "car_parts"(
  "quality_grade",
  "is_active"
);
CREATE INDEX "car_parts_slug_index" on "car_parts"("slug");
CREATE UNIQUE INDEX "car_parts_slug_unique" on "car_parts"("slug");
CREATE TABLE IF NOT EXISTS "affiliate_clicks"(
  "id" integer primary key autoincrement not null,
  "part_id" integer not null,
  "brand" varchar not null,
  "category" varchar not null,
  "user_agent" text,
  "referrer" varchar,
  "ip_address" varchar not null,
  "session_id" varchar not null,
  "click_id" varchar not null,
  "timestamp" datetime not null,
  "converted" tinyint(1) not null default '0',
  "conversion_date" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("part_id") references "car_parts"("id") on delete cascade
);
CREATE INDEX "affiliate_clicks_click_id_index" on "affiliate_clicks"(
  "click_id"
);
CREATE INDEX "affiliate_clicks_part_id_index" on "affiliate_clicks"("part_id");
CREATE INDEX "affiliate_clicks_brand_index" on "affiliate_clicks"("brand");
CREATE INDEX "affiliate_clicks_category_index" on "affiliate_clicks"(
  "category"
);
CREATE INDEX "affiliate_clicks_converted_index" on "affiliate_clicks"(
  "converted"
);
CREATE INDEX "affiliate_clicks_created_at_index" on "affiliate_clicks"(
  "created_at"
);
CREATE UNIQUE INDEX "affiliate_clicks_click_id_unique" on "affiliate_clicks"(
  "click_id"
);
CREATE TABLE IF NOT EXISTS "affiliate_commissions"(
  "id" integer primary key autoincrement not null,
  "click_id" varchar not null,
  "part_id" integer not null,
  "brand" varchar not null,
  "category" varchar not null,
  "order_id" varchar not null,
  "customer_email" varchar not null,
  "purchase_amount" numeric not null,
  "currency" varchar not null,
  "commission_rate" numeric not null,
  "commission_amount" numeric not null,
  "status" varchar check("status" in('pending', 'paid', 'cancelled')) not null default 'pending',
  "purchase_date" datetime not null,
  "payment_date" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("part_id") references "car_parts"("id") on delete cascade
);
CREATE INDEX "affiliate_commissions_click_id_index" on "affiliate_commissions"(
  "click_id"
);
CREATE INDEX "affiliate_commissions_part_id_index" on "affiliate_commissions"(
  "part_id"
);
CREATE INDEX "affiliate_commissions_brand_index" on "affiliate_commissions"(
  "brand"
);
CREATE INDEX "affiliate_commissions_category_index" on "affiliate_commissions"(
  "category"
);
CREATE INDEX "affiliate_commissions_status_index" on "affiliate_commissions"(
  "status"
);
CREATE INDEX "affiliate_commissions_purchase_date_index" on "affiliate_commissions"(
  "purchase_date"
);
CREATE INDEX "affiliate_commissions_order_id_index" on "affiliate_commissions"(
  "order_id"
);
CREATE TABLE IF NOT EXISTS "orders"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "order_number" varchar not null,
  "status" varchar check("status" in('pending', 'confirmed', 'shipped', 'delivered', 'cancelled', 'refunded')) not null default 'pending',
  "subtotal" numeric not null,
  "tax_amount" numeric not null default '0',
  "shipping_amount" numeric not null default '0',
  "discount_amount" numeric not null default '0',
  "total_amount" numeric not null,
  "currency" varchar not null default 'USD',
  "payment_method" varchar,
  "payment_status" varchar check("payment_status" in('pending', 'paid', 'failed', 'refunded')) not null default 'pending',
  "shipping_address" text,
  "billing_address" text,
  "customer_info" text,
  "notes" text,
  "tracking_number" varchar,
  "shipped_at" datetime,
  "delivered_at" datetime,
  "cancelled_at" datetime,
  "cancellation_reason" text,
  "refund_amount" numeric,
  "refunded_at" datetime,
  "refund_reason" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "orders_user_id_index" on "orders"("user_id");
CREATE INDEX "orders_status_index" on "orders"("status");
CREATE INDEX "orders_payment_status_index" on "orders"("payment_status");
CREATE INDEX "orders_created_at_index" on "orders"("created_at");
CREATE INDEX "orders_order_number_index" on "orders"("order_number");
CREATE UNIQUE INDEX "orders_order_number_unique" on "orders"("order_number");
CREATE TABLE IF NOT EXISTS "order_items"(
  "id" integer primary key autoincrement not null,
  "order_id" integer not null,
  "part_id" integer,
  "part_name" varchar not null,
  "part_brand" varchar,
  "part_number" varchar,
  "part_description" text,
  "part_image_url" varchar,
  "part_category" varchar,
  "quantity" integer not null,
  "unit_price" numeric not null,
  "total_price" numeric not null,
  "source" varchar not null default 'carwise',
  "affiliate_url" varchar,
  "tracking_data" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("order_id") references "orders"("id") on delete cascade
);
CREATE INDEX "order_items_order_id_index" on "order_items"("order_id");
CREATE INDEX "order_items_part_id_index" on "order_items"("part_id");
CREATE INDEX "order_items_part_name_index" on "order_items"("part_name");
CREATE INDEX "order_items_part_brand_index" on "order_items"("part_brand");
CREATE INDEX "order_items_part_category_index" on "order_items"(
  "part_category"
);
CREATE TABLE IF NOT EXISTS "wishlist"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "part_id" integer,
  "part_name" varchar not null,
  "part_brand" varchar,
  "part_number" varchar,
  "part_description" text,
  "part_image_url" varchar,
  "part_category" varchar,
  "part_price" numeric not null,
  "part_currency" varchar not null default 'USD',
  "source" varchar not null default 'carwise',
  "affiliate_url" varchar,
  "notes" text,
  "priority" varchar check("priority" in('low', 'medium', 'high')) not null default 'medium',
  "notification_enabled" tinyint(1) not null default '0',
  "price_alert_threshold" numeric,
  "added_at" datetime not null default CURRENT_TIMESTAMP,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "wishlist_user_id_index" on "wishlist"("user_id");
CREATE INDEX "wishlist_part_id_index" on "wishlist"("part_id");
CREATE INDEX "wishlist_part_name_index" on "wishlist"("part_name");
CREATE INDEX "wishlist_part_brand_index" on "wishlist"("part_brand");
CREATE INDEX "wishlist_part_category_index" on "wishlist"("part_category");
CREATE INDEX "wishlist_priority_index" on "wishlist"("priority");
CREATE INDEX "wishlist_added_at_index" on "wishlist"("added_at");
CREATE UNIQUE INDEX "wishlist_user_id_part_id_part_name_unique" on "wishlist"(
  "user_id",
  "part_id",
  "part_name"
);
CREATE TABLE IF NOT EXISTS "search_history"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "search_query" varchar not null,
  "search_type" varchar not null default 'car_parts',
  "search_category" varchar,
  "search_filters" text,
  "results_count" integer not null default '0',
  "search_duration" numeric,
  "ip_address" varchar,
  "user_agent" text,
  "referrer" varchar,
  "session_id" varchar,
  "device_type" varchar,
  "browser" varchar,
  "operating_system" varchar,
  "search_timestamp" datetime not null default CURRENT_TIMESTAMP,
  "is_successful" tinyint(1) not null default '1',
  "error_message" text,
  "search_source" varchar not null default 'web',
  "search_context" varchar,
  "additional_data" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "search_history_user_id_index" on "search_history"("user_id");
CREATE INDEX "search_history_search_query_index" on "search_history"(
  "search_query"
);
CREATE INDEX "search_history_search_type_index" on "search_history"(
  "search_type"
);
CREATE INDEX "search_history_search_category_index" on "search_history"(
  "search_category"
);
CREATE INDEX "search_history_search_timestamp_index" on "search_history"(
  "search_timestamp"
);
CREATE INDEX "search_history_is_successful_index" on "search_history"(
  "is_successful"
);
CREATE INDEX "search_history_results_count_index" on "search_history"(
  "results_count"
);
CREATE INDEX "search_history_device_type_index" on "search_history"(
  "device_type"
);
CREATE INDEX "search_history_browser_index" on "search_history"("browser");
CREATE INDEX "search_history_session_id_index" on "search_history"(
  "session_id"
);
CREATE INDEX "search_history_search_source_index" on "search_history"(
  "search_source"
);
CREATE TABLE IF NOT EXISTS "saved_searches"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "search_name" varchar not null,
  "search_query" varchar not null,
  "search_type" varchar not null default 'car_parts',
  "search_category" varchar,
  "search_filters" text,
  "search_description" text,
  "is_public" tinyint(1) not null default '0',
  "is_favorite" tinyint(1) not null default '0',
  "tags" text,
  "notification_enabled" tinyint(1) not null default '0',
  "notification_frequency" varchar not null default 'weekly',
  "last_searched_at" datetime,
  "search_count" integer not null default '0',
  "results_count" integer not null default '0',
  "average_duration" numeric,
  "success_rate" numeric not null default '0',
  "search_source" varchar not null default 'web',
  "search_context" varchar,
  "additional_data" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "saved_searches_user_id_index" on "saved_searches"("user_id");
CREATE INDEX "saved_searches_search_name_index" on "saved_searches"(
  "search_name"
);
CREATE INDEX "saved_searches_search_query_index" on "saved_searches"(
  "search_query"
);
CREATE INDEX "saved_searches_search_type_index" on "saved_searches"(
  "search_type"
);
CREATE INDEX "saved_searches_search_category_index" on "saved_searches"(
  "search_category"
);
CREATE INDEX "saved_searches_is_public_index" on "saved_searches"("is_public");
CREATE INDEX "saved_searches_is_favorite_index" on "saved_searches"(
  "is_favorite"
);
CREATE INDEX "saved_searches_notification_enabled_index" on "saved_searches"(
  "notification_enabled"
);
CREATE INDEX "saved_searches_last_searched_at_index" on "saved_searches"(
  "last_searched_at"
);
CREATE INDEX "saved_searches_search_count_index" on "saved_searches"(
  "search_count"
);
CREATE INDEX "saved_searches_success_rate_index" on "saved_searches"(
  "success_rate"
);
CREATE INDEX "saved_searches_search_source_index" on "saved_searches"(
  "search_source"
);
CREATE UNIQUE INDEX "saved_searches_user_id_search_name_unique" on "saved_searches"(
  "user_id",
  "search_name"
);

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2025_09_05_215911_create_personal_access_tokens_table',1);
INSERT INTO migrations VALUES(5,'2025_09_05_220502_create_cars_table',1);
INSERT INTO migrations VALUES(6,'2025_09_05_220506_create_diagnoses_table',1);
INSERT INTO migrations VALUES(7,'2025_09_05_220510_create_mechanics_table',1);
INSERT INTO migrations VALUES(8,'2025_09_05_220606_add_role_to_users_table',1);
INSERT INTO migrations VALUES(9,'2025_09_06_035843_create_diagnosis_sessions_table',1);
INSERT INTO migrations VALUES(10,'2025_09_06_035904_create_diagnosis_media_table',1);
INSERT INTO migrations VALUES(11,'2025_09_06_035910_create_diagnosis_results_table',1);
INSERT INTO migrations VALUES(12,'2025_09_06_041354_create_user_sessions_table',1);
INSERT INTO migrations VALUES(13,'2025_09_06_041400_create_user_activity_logs_table',1);
INSERT INTO migrations VALUES(14,'2025_09_06_041407_create_user_preferences_table',1);
INSERT INTO migrations VALUES(15,'2025_09_06_041725_add_remaining_user_fields_to_users_table',1);
INSERT INTO migrations VALUES(16,'2025_09_06_045551_add_missing_columns_to_cars_table',1);
INSERT INTO migrations VALUES(17,'2025_09_23_214904_create_car_brands_table',1);
INSERT INTO migrations VALUES(18,'2025_09_23_214909_create_car_models_table',1);
INSERT INTO migrations VALUES(19,'2025_09_24_132840_create_currencies_table',1);
INSERT INTO migrations VALUES(20,'2025_09_24_132910_add_currency_preferences_to_users_table',1);
INSERT INTO migrations VALUES(21,'2025_09_24_135849_add_admin_role_to_users_table',1);
INSERT INTO migrations VALUES(22,'2025_09_25_000001_extend_mechanics_table',1);
INSERT INTO migrations VALUES(23,'2025_09_26_035130_create_mechanic_reviews_table',1);
INSERT INTO migrations VALUES(24,'2025_09_26_035629_create_review_helpfulness_table',1);
INSERT INTO migrations VALUES(25,'2025_09_26_203755_create_notifications_table',1);
INSERT INTO migrations VALUES(26,'2025_09_26_204340_create_payments_table',1);
INSERT INTO migrations VALUES(27,'2025_09_26_204443_create_subscriptions_table',1);
INSERT INTO migrations VALUES(28,'2025_09_26_204952_create_chat_conversations_table',1);
INSERT INTO migrations VALUES(29,'2025_09_26_205108_create_chat_messages_table',1);
INSERT INTO migrations VALUES(30,'2025_09_26_205442_create_appointments_table',1);
INSERT INTO migrations VALUES(31,'2025_09_27_041815_create_car_maintenance_history_table',1);
INSERT INTO migrations VALUES(32,'2025_09_27_041904_create_maintenance_notifications_table',1);
INSERT INTO migrations VALUES(33,'2025_09_27_042144_add_maintenance_tracking_to_cars_table',1);
INSERT INTO migrations VALUES(34,'2025_09_27_190401_create_car_images_table',1);
INSERT INTO migrations VALUES(35,'2025_09_28_130518_add_car_id_to_diagnosis_sessions_table',1);
INSERT INTO migrations VALUES(36,'2025_09_29_000002_add_missing_performance_indexes',2);
INSERT INTO migrations VALUES(37,'2025_09_29_192958_add_missing_user_fields_to_users_table',3);
INSERT INTO migrations VALUES(38,'2025_09_29_203906_create_car_parts_table',4);
INSERT INTO migrations VALUES(39,'2025_09_29_204130_create_diagnosis_suggested_parts_table',4);
INSERT INTO migrations VALUES(40,'2025_09_29_211414_create_authorized_companies_table',5);
INSERT INTO migrations VALUES(41,'2025_09_29_211448_add_authorized_company_to_car_parts_table',5);
INSERT INTO migrations VALUES(42,'2025_09_30_220326_create_affiliate_clicks_table',6);
INSERT INTO migrations VALUES(43,'2025_09_30_220331_create_affiliate_commissions_table',6);
INSERT INTO migrations VALUES(44,'2025_10_01_223523_create_orders_table',7);
INSERT INTO migrations VALUES(45,'2025_10_01_223544_create_order_items_table',7);
INSERT INTO migrations VALUES(46,'2025_10_01_224354_create_wishlist_table',8);
INSERT INTO migrations VALUES(47,'2025_10_02_030915_create_search_history_table',9);
INSERT INTO migrations VALUES(48,'2025_10_02_032050_create_saved_searches_table',10);
