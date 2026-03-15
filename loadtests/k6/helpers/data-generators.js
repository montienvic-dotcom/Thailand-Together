/**
 * Generate random test data for load test scenarios.
 */

export function randomEmail() {
    const id = Math.random().toString(36).substring(2, 10);
    return `tourist_${id}@loadtest.com`;
}

export function randomName() {
    const firstNames = ['Alex', 'Sam', 'Chris', 'Jordan', 'Taylor', 'Morgan', 'Casey', 'Riley'];
    const lastNames = ['Tourist', 'Traveler', 'Explorer', 'Wanderer', 'Visitor'];
    return `${firstNames[Math.floor(Math.random() * firstNames.length)]} ${lastNames[Math.floor(Math.random() * lastNames.length)]}`;
}

export function randomCoordinates() {
    // Pattaya area: lat 12.87-12.97, lng 100.85-100.92
    return {
        lat: 12.87 + Math.random() * 0.10,
        lng: 100.85 + Math.random() * 0.07,
    };
}

export function randomBookingDate() {
    const future = new Date();
    future.setDate(future.getDate() + Math.floor(Math.random() * 30) + 1);
    return future.toISOString().split('T')[0];
}

export function randomMerchantId() {
    return Math.floor(Math.random() * 100) + 1;
}

export function randomJourneyCode() {
    const codes = ['J001', 'J002', 'J003', 'J004', 'J005', 'J006', 'J007', 'J008'];
    return codes[Math.floor(Math.random() * codes.length)];
}

export function randomPartySize() {
    return Math.floor(Math.random() * 6) + 1;
}
