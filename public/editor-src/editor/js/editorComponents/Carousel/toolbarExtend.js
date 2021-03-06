import { t } from "visual/utils/i18n";
import { hideToolbar } from "visual/component-new/Toolbar/index";

export function getItemsForDesktop(v) {
  return [
    {
      id: "duplicate",
      type: "button",
      icon: "nc-duplicate",
      title: t("Duplicate"),
      roles: ["admin"],
      position: 200,
      onChange: () => {
        return {
          columns: ++v.columns
        };
      }
    },
    ...(v.columns > v.slidesToShow
      ? [
          {
            id: "remove",
            type: "button",
            title: t("Delete"),
            roles: ["admin"],
            icon: "nc-trash",
            position: 250,
            onChange: () => {
              hideToolbar();

              return {
                columns: --v.columns
              };
            }
          }
        ]
      : [])
  ];
}

export function getItemsForMobile(v) {
  return [];
}
