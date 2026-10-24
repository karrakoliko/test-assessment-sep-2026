CREATE TABLE categories
(
    id          BIGINT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    description LONGTEXT     NOT NULL,
    created_at  DATETIME(3)  NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    CONSTRAINT categories__title_unique
        UNIQUE (title)
)
    ENGINE = InnoDB;

CREATE TABLE articles
(
    id          BIGINT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    description LONGTEXT     NULL,
    content     LONGTEXT     NULL,
    pic_uri     TEXT         NULL,
    category_id BIGINT       NOT NULL,
    created_at  DATETIME(3)  NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    CONSTRAINT articles__category_fk
        FOREIGN KEY (category_id)
            REFERENCES categories (id)
            ON DELETE RESTRICT,

    INDEX articles__category_created_id_index (
                                               category_id,
                                               created_at DESC,
                                               id DESC
        )
)
    ENGINE = InnoDB;