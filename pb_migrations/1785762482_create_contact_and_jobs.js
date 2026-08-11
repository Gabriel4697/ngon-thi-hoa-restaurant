/// <reference path="../pb_data/types.d.ts" />

migrate(
  (app) => {
    // Contact messages collection
    const contacts = new Collection({
      type: "base",
      name: "contact_messages",
      listRule: null,
      viewRule: null,
      createRule: "",
      updateRule: null,
      deleteRule: null,
      fields: [
        { name: "full_name", type: "text", required: true, max: 200 },
        { name: "email", type: "email", required: true },
        { name: "phone", type: "text", max: 30 },
        { name: "subject", type: "text", max: 300 },
        { name: "message", type: "text", max: 5000 },
        { name: "created", type: "autodate", onCreate: true, onUpdate: false },
        { name: "updated", type: "autodate", onCreate: true, onUpdate: true },
      ],
    });
    app.save(contacts);

    // Job applications collection
    const jobs = new Collection({
      type: "base",
      name: "job_applications",
      listRule: null,
      viewRule: null,
      createRule: "",
      updateRule: null,
      deleteRule: null,
      fields: [
        { name: "full_name", type: "text", required: true, max: 200 },
        { name: "email", type: "email", required: true },
        { name: "phone", type: "text", required: true, max: 30 },
        { name: "position", type: "text", required: true, max: 200 },
        { name: "cover_letter", type: "text", max: 5000 },
        { name: "cv_file", type: "file", maxSelect: 1, maxSize: 5242880, mimeTypes: ["application/pdf","application/msword","application/vnd.openxmlformats-officedocument.wordprocessingml.document","image/jpeg","image/png"] },
        { name: "created", type: "autodate", onCreate: true, onUpdate: false },
        { name: "updated", type: "autodate", onCreate: true, onUpdate: true },
      ],
    });
    app.save(jobs);
  },
  (app) => {
    try { app.delete(app.findCollectionByNameOrId("contact_messages")); } catch (_) {}
    try { app.delete(app.findCollectionByNameOrId("job_applications")); } catch (_) {}
  }
);
