//
//  SyncObserverDelegate.h
//  Track
//
//  Created by Bartek on 13/02/2016.
//  Copyright © 2016 Bartlomiej Kliszczyk. All rights reserved.
//

#import <Foundation/Foundation.h>

/**
 *  Abbreviation for Firebase.FEventType
 */
typedef NS_OPTIONS(NSUInteger, DataChange) {
    /**
     *  Firebase.FEventTypeValue
     */
    DataAny,
    /**
     *  Firebase.FEventTypeChildRemoved
     */
    DataRemoved,
    /**
     *  Firebase.FEventTypeChildAdded
     */
    DataAdded,
    /**
     *  Firebase.FEventTypeChildChanged
     */
    DataChanged,
    /**
     *  Firebase.FEventTypeChildMoved
     */
    DataMoved
};

/**
 *  @brief Completion block returning NSDictionary
 *
 *  @param NSDictionary data
 */
typedef void (^completionBlock)( NSDictionary * _Nullable data);

/**
 *  @brief Completion block returning key, object pairs as two separate objects. Mainly used for UICollectionView
 *
 *  @param indexes NSArray - holds keys of data items in NSDictionary objects
 *  @param objects NSDictionary - any objects that indexes map to
 */
typedef void (^keysAndObjectsCompletionBlock)(NSArray * _Nullable indexes, NSDictionary * _Nullable objects);

/**
 *  @brief Completion block returning FDataSnapshot
 *
 *  @param snapshot FDataSnapshot
 */
typedef void (^completionBlockWithSnapshot)(FDataSnapshot* _Nullable snapshot);

/**
 *  @brief Complation block returning BOOL success of some operation
 *
 *  @param success returns BOOL status of some operation
 */
typedef void (^successCompletionBlock)(BOOL success, NSError * _Nullable error);

#pragma mark SyncObserverDelegate

@protocol SyncObserverDelegate <NSObject>

@optional

#pragma mark - save

/**
 *  @brief Encode object as JSON and save it to firebase
 */
- (void) save;

/**
 *  @brief Convinience method mainly to be overriden to save single piece of data
 *
 *  @param data
 */
- (void) save:(nonnull id)data;

/**
 *  @brief Save data to db by providing NSDictionary structure
 *
 *  @param data NSDictionary json like structure
 */
- (void) saveWithStructure:(nonnull id)data;

/**
 *  @brief Save key value pair to db
 *
 *  @param key
 *  @param value
 */
- (void) saveWithKey:(nonnull NSString *)key withValue:(nullable id)value;

/**
 *  @brief Save key value pair to db
 *
 *  @see saveWithKey:key:withValue
 *
 *  @param key        key
 *  @param value      value
 *  @param complation success block
 */
- (void) saveWithKey:(nonnull NSString *)key withValue:(nonnull id)value withComplationBlock:(nullable successCompletionBlock)complation;

#pragma mark - monitorSingleEventBy

/**
 *  @brief Monitor single changes, triggers @property singleEventCompletionBlock
 *
 *  @param dataEvent DataChange
 */
- (void) monitorSingleEventBy:(DataChange)dataEvent;

/**
 *  @brief Monitor single changes
 *
 *  @param dataEvent  DataChange
 *  @param completion completionBlock
 */
- (void) monitorSingleEventBy:(DataChange)dataEvent withCompletionBlock:(nullable completionBlock)completion;

/**
 *  @brief Monitor single changes
 *
 *  @param dataEvent  DataChange
 *  @param completion keysAndObjectsCompletionBlock
 */
- (void) monitorSingleEventBy:(DataChange)dataEvent withKeysAndObjectsCompletionBlock:(nullable keysAndObjectsCompletionBlock)completion;

#pragma mark - monitorContinuousEventBy

/**
 *  @brief Monitor continuous changes, triggers @property continuousEventCompletionBlock
 *
 *  @param dataEvent continuousEventCompletionBlock
 */
- (void) monitorContinuousEventBy:(DataChange)dataEvent;

/**
 *  @brief Monitor continuous changes
 *
 *  @param dataEvent  DataChange
 *  @param completion completionBlock
 */
- (void) monitorContinuousEventBy:(DataChange)dataEvent withCompletionBlock:(nullable completionBlock)completion;

/**
 *  @brief Monitor continuous changes
 *
 *  @param dataEvent  DataChange
 *  @param completion completionBlockWithSnapshot
 */
- (void) monitorContinuousEventBy:(DataChange)dataEvent withSnapshotCompletionBlock:(nullable completionBlockWithSnapshot)completion;

/**
 *  @brief Monitor continuous changes
 *
 *  @param dataEvent  DataChange
 *  @param completion keysAndObjectsCompletionBlock
 */
- (void) monitorContinuousEventBy:(DataChange)dataEvent withKeysAndObjectsCompletionBlock:(nullable keysAndObjectsCompletionBlock)completion;

/**
 *  @brief Triggered when all registered monitors are removed
 */
- (void) didRemoveRegisteredMonitors;

@end