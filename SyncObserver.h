/**
 *  @author Bartlomiej T. Kliszczyk
 *  @copyright Copyright © 2016 Pixot. All rights reserved
 *  @version 0.1
 *
 *  @class SyncObserver
 *
 *  @brief Base class for retrieving and syncing data to db
 */

#import <Foundation/Foundation.h>
#import "SyncObserverDelegate.h"
#import <objc/runtime.h>

#pragma mark -
#pragma mark SyncObserver


@interface SyncObserver : NSObject

#pragma mark - Properties
/**
 *  @brief User auth data (Firebase.AuthData)
 */
@property (nullable, nonatomic, strong) FAuthData *authData;

/**
 * @brief Root path to Firebase db
 */
@property (nullable, nonatomic, strong) Firebase *rootRef;

/**
 * @brief Child path to Firebase db
 */
@property (nullable, nonatomic, strong) Firebase *childRef;

/**
 *  @brief Child path with auto id to Firebase db
 */
@property (nullable, nonatomic, strong) Firebase *childRefAutoId;

/**
 *  @brief Child path with auto id and user uid as parent to Firebase db
 */
@property (nullable, nonatomic, strong) Firebase *childRefWithUIDWithAutoId;

/**
 *  @brief Child path with user uid to Firebase db
 */
@property (nullable, nonatomic, strong) Firebase *childRefWithUID;

/**
 * @brief Query path to Firebase db
 */
@property (nullable, nonatomic, strong) FQuery *query;

/**
 *  @brief Current firebase handle used for removing monitors
 */
@property (nullable, nonatomic) FirebaseHandle *handle;

/**
 * @brief Holds an array of references to all registered monitors/observers connected to Firebase
 */
@property (nullable, nonatomic, strong) NSMutableArray *registeredMonitors;

/**
 * @brief Block triggered by calling a method without block in the parameter signature
 */
@property (nullable, nonatomic, copy) completionBlock singleEventCompletionBlock;

/**
 *  @brief Block triggered by calling a method without block in the parameter signature
 */
@property (nullable, nonatomic, copy) completionBlock continuousEventCompletionBlock;

/**
 *  @brief Block triggered by calling a method without block in the parameter signature
 */
@property (nullable, nonatomic, copy) keysAndObjectsCompletionBlock keysObjectsComplationBlock;

/**
 *  @brief Block triggered by calling a method without block in the parameter signature
 */
@property (nullable, nonatomic, copy) successCompletionBlock successBlock;

/**
 *  @brief SyncObserverDelegate
 */
@property (nullable, nonatomic, weak) id<SyncObserverDelegate>delegate;

/**
 *  @brief Factory key
 */
@property (nonatomic, nonnull, strong) NSString *factoryKey;

#pragma mark - Public instance methods

/**
 *  @brief Singleton manager
 *
 *  @return (SyncObserver*)id
 */
+ (nonnull instancetype)sharedManager;

/**
 *  @brief Remove all registered monitors
 */
- (void) removeAllRegisteredMonitors;

/**
 *  @brief Remove all registered monitors with complation block
 *
 *  @param complationBlock complationBlock(success)
 */
- (void) removeAllRegisteredMonitorsWithCompletionBlock:(_Nullable successCompletionBlock)complationBlock;

#pragma mark - save

/**
 *  @brief Encode object as JSON and save it to firebase
 */
- (void) save;

- (void) saveWithAutoId;

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
 *  @brief Save data to db with auto id by providing NSDictionary structure
 *
 *  @param data NSDictionary json like structure
 */
- (void) saveWithAutoIdStructure:(nonnull id)data;

/**
 *  @brief Save global/user independent data to db by providing NSDictionary structure
 *
 *  @param data NSDictionary json like structure
 */
- (void) saveGlobalWithStructure:(nonnull id)data;

/**
 *  @brief Save key value pair to db
 *
 *  @param key
 *  @param value
 */
- (void) saveWithKey:(nonnull NSString*)key withValue:(_Nullable id)value;

/**
 *  @brief Save key value pair to db by auto id
 *
 *  @param key
 *  @param value
 */
- (void) saveWithAutoIdWithChildKey:(nonnull NSString*)key withValue:(_Nullable id)value;


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

/**
 *  @brief Save key value pair to db
 *
 *  @see saveWithKey:key:withValue
 *
 *  @param key        key
 *  @param value      value
 *  @param complation success block
 */
- (void) saveWithAutoIdWithChildKey:(nonnull NSString *)key withValue:(nonnull id)value withComplationBlock:(nullable successCompletionBlock)complation;

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
- (void) monitorSingleEventBy:(DataChange)dataEvent withCompletionBlock:(_Nullable completionBlock)completion;

/**
 *  @brief Monitor single changes
 *
 *  @param dataEvent  DataChange
 *  @param completion keysAndObjectsCompletionBlock
 */
- (void) monitorSingleEventBy:(DataChange)dataEvent withKeysAndObjectsCompletionBlock:(_Nullable keysAndObjectsCompletionBlock)completion;

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
- (void) monitorContinuousEventBy:(DataChange)dataEvent withCompletionBlock:(_Nullable completionBlock)completion;

/**
 *  @brief Monitor continuous changes
 *
 *  @param dataEvent  DataChange
 *  @param completion keysAndObjectsCompletionBlock
 */
- (void) monitorContinuousEventBy:(DataChange)dataEvent withKeysAndObjectsCompletionBlock:(_Nullable keysAndObjectsCompletionBlock)completion;

/**
 *  @brief Monitor continuous changes
 *
 *  @param dataEvent  DataChange
 *  @param completion completionBlockWithSnapshot
 */
- (void) monitorContinuousEventBy:(DataChange)dataEvent withSnapshotCompletionBlock:(nullable completionBlockWithSnapshot)completion;

/**
 *  @brief Add constraint to the query
 *
 *  @param constraint
 *
 *  @return Firebase.Path
 */
- (nonnull instancetype) constraintBy:(NSString* _Nonnull)constraint;

/**
 *  @brief Add order to the query
 *
 *  @param order
 *
 *  @return Firebase.Path
 */
- (nonnull id) orderBy:(NSString * _Nonnull)order;

/**
 *  @brief Changes DataChange to FEventType
 *
 *  @param data
 *
 *  @return FEventType
 */
- (FEventType)edc:(DataChange)data;

/**
 *  @brief Override firebase parent
 *
 *  @return
 */
+ (nullable NSString*)className;


@end
