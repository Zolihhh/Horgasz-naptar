function normalizeErrorMessage(value) {
  if (Array.isArray(value)) {
    return String(value[0] || "").trim();
  }

  if (typeof value === "string") {
    return value.trim();
  }

  return "";
}

export function extractFieldErrors(error) {
  const errors = error?.response?.data?.errors;
  if (!errors || typeof errors !== "object") {
    return {};
  }

  return Object.entries(errors).reduce((acc, [field, value]) => {
    const message = normalizeErrorMessage(value);
    if (message) {
      acc[field] = message;
    }
    return acc;
  }, {});
}

export function getFirstFieldError(error) {
  const fieldErrors = extractFieldErrors(error);
  return Object.values(fieldErrors)[0] || "";
}

export function isGenericValidationMessage(message) {
  const normalized = String(message || "").trim();
  return normalized === "Validációs hiba történt." || normalized === "The given data was invalid.";
}

export function getApiErrorMessage(error, fallback) {
  const firstFieldError = getFirstFieldError(error);
  if (firstFieldError) {
    return firstFieldError;
  }

  const message = error?.response?.data?.message;
  if (typeof message === "string" && message.trim() && !isGenericValidationMessage(message)) {
    return message.trim();
  }

  return fallback;
}
