<?php


namespace App;


trait SyncWithFirebase
{
    public static function bootSyncWithFirebase()
    {
        static::created(function ($model) {
            $model->saveToFirebase('set');
        });
        static::updated(function ($model) {
            $model->saveToFirebase('update');
        });
        static::deleted(function ($model) {
            $model->saveToFirebase('delete');
        });
    }

    public function getFirebaseSyncData()
    {
        if($fresh = $this->fresh())
        {
            return $fresh->toArray();
        }
        return [];
    }

    public function saveToFirebase($mode)
    {
        //initialize realtime database
        $database = app('firebase.database');

        //getting table path an id
        $path = $this->getTable(). '/' . $this->getKey();

        if ($mode==='set'){
            $database->getReference($path)->set($this->getFirebaseSyncData());
        } elseif ($mode === 'update') {
            $database->getReference($path)->update($this->getFirebaseSyncData());
        } elseif ($mode === 'delete') {
            $database->getReference($path)->delete($this->getFirebaseSyncData());
        }
    }

}
