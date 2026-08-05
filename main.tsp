import "@typespec/http";
import "./specs/models.tsp";

using TypeSpec.Http;
using CallMeInTime.Models;

namespace CallMeInTimeApi {
  @service(#{
    title: "Call me in time",
  })
  @server("http://localhost:8000/api", "Laravel API")
  namespace Api {
    @route("/event-types")
    interface EventTypes {
      @get
      list(): EventType[];

      @post
      create(@body body: CreateEventTypeRequest): EventType;

      @get
      @route("{id}")
      get(@path id: int32): EventType;

      @get
      @route("{id}/slots")
      getSlots(@path id: int32): SlotsResponse;
    }

    @route("/bookings")
    interface Bookings {
      @get
      list(): Booking[];

      @post
      create(@body body: CreateBookingRequest): Booking;

      @get
      @route("{id}")
      get(@path id: int32): Booking;
    }
  }
}
