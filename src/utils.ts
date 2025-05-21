/**
 * Utility functions for the KCC Digital Signage system
 */

/**
 * Formats a date string into a human-readable format
 * @param date - The date to format
 * @returns Formatted date string
 */
export function formatDate(date: Date): string {
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Validates if a string is a valid URL
 * @param url - The URL to validate
 * @returns True if the URL is valid, false otherwise
 */
export function isValidUrl(url: string): boolean {
    try {
        new URL(url);
        return true;
    } catch {
        return false;
    }
} 