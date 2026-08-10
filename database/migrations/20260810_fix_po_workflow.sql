-- Grant the Procurement role access to the Purchase Order menu.
-- Safe to run repeatedly because role_menus has a unique key on (role_id, menu_id).

INSERT INTO role_menus (
    role_id,
    menu_id,
    can_view,
    can_create,
    can_edit,
    can_delete
)
SELECT
    r.id,
    m.id,
    1,
    0,
    0,
    0
FROM roles r
JOIN menus m ON m.title = 'Management Order'
WHERE r.role_code = 'procurement'
ON DUPLICATE KEY UPDATE
    can_view = VALUES(can_view);

INSERT INTO role_menus (
    role_id,
    menu_id,
    can_view,
    can_create,
    can_edit,
    can_delete
)
SELECT
    r.id,
    m.id,
    1,
    1,
    1,
    0
FROM roles r
JOIN menus m ON m.url = 'purchaseOrders'
WHERE r.role_code = 'procurement'
ON DUPLICATE KEY UPDATE
    can_view = VALUES(can_view),
    can_create = VALUES(can_create),
    can_edit = VALUES(can_edit),
    can_delete = VALUES(can_delete);
