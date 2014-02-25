-- Get all user's file
select
    *PREFIX*filecache.`path` as path,
    *PREFIX*storages.id as storage,
    *PREFIX*filecache.encrypted as encrypted,
    *PREFIX*filecache.`name` as name,
    *PREFIX*mimetypes.mimetype as mimetype
from
    *PREFIX*filecache, *PREFIX*mimetypes, *PREFIX*storages
where
    *PREFIX*filecache.mimetype = *PREFIX*mimetypes.id
    and
    *PREFIX*filecache.storage = *PREFIX*storages.numeric_id
    and
    *PREFIX*filecache.`path` like 'files%'
    and
    *PREFIX*filecache.`path` not like 'files_trashbin%'
    and
    *PREFIX*storages.id = ?
order by
    *PREFIX*filecache.storage,
    *PREFIX*filecache.`path`;
