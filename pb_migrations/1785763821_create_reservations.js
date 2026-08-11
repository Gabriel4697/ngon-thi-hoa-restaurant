/// <reference path="../pb_data/types.d.ts" />

migrate(
  (app) => {
    const reservations = new Collection({
      type: "base",
      name: "reservations",
      listRule: null,
      viewRule: null,
      createRule: "",
      updateRule: null,
      deleteRule: null,
      fields: [
        { name: "full_name", type: "text", required: true, max: 200 },
        { name: "email", type: "email", required: true },
        { name: "phone", type: "text", required: true, max: 30 },
        { name: "reservation_date", type: "text", required: true, max: 20 },
        { name: "reservation_time", type: "text", required: true, max: 10 },
        { name: "guests", type: "number", required: true, min: 1 },
        { name: "notes", type: "text", max: 1000 },
        { name: "created", type: "autodate", onCreate: true, onUpdate: false },
        { name: "updated", type: "autodate", onCreate: true, onUpdate: true },
      ],
    });
    app.save(reservations);
  },
  (app) => {
    try { app.delete(app.findCollectionByNameOrId("reservations")); } catch (_) {}
  }
);
