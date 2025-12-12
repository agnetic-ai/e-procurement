<?php
class MenusModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetMenus($roleCode)
    {
        $query = "WITH RECURSIVE menuTree AS (
                    SELECT 
                        m.id,
                        m.title,
                        m.icon,
                        m.url,
                        m.parent_id AS parentId,
                        m.menu_order AS menuOrder,
                        1 AS level,
                        CAST(m.id AS CHAR(255)) AS path
                    FROM menus m
                    WHERE m.parent_id IS NULL
                    AND m.is_active = TRUE
                    
                    UNION ALL
                    
                    SELECT 
                        m.id,
                        m.title,
                        m.icon,
                        m.url,
                        m.parent_id AS parentId,
                        m.menu_order AS menuOrder,
                        mt.level + 1 AS level,
                        CONCAT(mt.path, ' > ', m.id) AS path
                    FROM menus m
                    INNER JOIN menuTree mt ON m.parent_id = mt.id
                    WHERE m.is_active = TRUE
                )
                SELECT 
                    mt.id,
                    mt.title,
                    mt.icon,
                    mt.url,
                    mt.parentId,
                    mt.menuOrder,
                    mt.level,
                    mt.path,
                    rm.can_view AS canView,
                    rm.can_create AS canCreate,
                    rm.can_edit AS canEdit,
                    rm.can_delete AS canDelete,
                    r.role_code AS roleCode,
                    r.role_name AS roleName
                FROM menuTree mt
                INNER JOIN role_menus rm ON mt.id = rm.menu_id
                INNER JOIN roles r ON rm.role_id = r.id
                WHERE r.role_code = :role_code
                AND rm.can_view = TRUE
                ORDER BY 
                    CASE WHEN mt.parentId IS NULL THEN mt.menuOrder ELSE mt.parentId END,
                    mt.level,
                    mt.menuOrder;";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':role_code' => $roleCode]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
