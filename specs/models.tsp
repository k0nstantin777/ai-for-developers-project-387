import "@typespec/http";

using TypeSpec.Http;

namespace CallMeInTime.Models {
  model EventType {
    id: int32;
    name: string;
    description: string;
    duration: int32;
    createdAt: utcDateTime;
    updatedAt: utcDateTime;
  }

  model CreateEventTypeRequest {
    name: string;
    description: string;
    duration: int32;
  }

  model Booking {
    id: int32;
    eventTypeId: int32;
    guestName: string;
    guestEmail: string;
    startTime: utcDateTime;
    endTime: utcDateTime;
    eventType?: EventType;
    createdAt: utcDateTime;
    updatedAt: utcDateTime;
  }

  model CreateBookingRequest {
    eventTypeId: int32;
    guestName: string;
    guestEmail: string;
    startTime: utcDateTime;
  }

  model Slot {
    startTime: utcDateTime;
    endTime: utcDateTime;
  }

  model SlotsResponse {
    slots: Slot[];
  }
}
