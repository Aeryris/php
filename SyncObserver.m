//
//  SyncObserver.m
//  Track
//
//  Created by Bartek on 13/02/2016.
//  Base on Tof Templates
//  Copyright © 2016 Bartlomiej Kliszczyk. All rights reserved.
//

#import "SyncObserver.h"




#pragma mark SyncObserver
#pragma mark -

@interface SyncObserver () {

}

- (NSString*)buildChildPathByChildClass;

- (FEventType)firebaseEventTypeWithDataChangeType:(DataChange)data;

@end

@implementation SyncObserver

- (instancetype)init{
    self = [super init];
    if(self){
        
        /**
         *  @brief Setup Firebase root reference
         */
        self.rootRef = [[Firebase alloc] initWithUrl:PXFirebaseUrl];
    
        /**
         *  @brief Child ref
         */
        self.childRef = [self.rootRef childByAppendingPath:[self buildChildPathByChildClass]];
        
        /**
         *  @brief Child ref with auto id
         */
        self.childRefAutoId = [self.childRef childByAutoId];
        
        /**
         *  @brief Query ref
         */
        self.query = nil;
        
        if(self.rootRef.authData != nil){
            
            /**
             *  @brief Firebase AuthData
             */
            self.authData = self.rootRef.authData;
            
            /**
             *  @brief Child with UID as parent and childAutoID
             */
            self.childRefWithUIDWithAutoId = [[self.childRef childByAppendingPath:self.authData.uid] childByAutoId];
            
            /**
             *  @brief Child with UID
             */
            self.childRefWithUID = [self.childRef childByAppendingPath:self.authData.uid];
            
        }
        
        self.registeredMonitors = [[self class] monitors];
        

    }
    return self;
}

+ (NSMutableArray*)monitors{
    static NSMutableArray *monitors;
    static dispatch_once_t onceToken;
    dispatch_once(&onceToken, ^{
        monitors = [[NSMutableArray alloc] init];
    });
    return monitors;
}

/**
 *  @brief Build child path automatically based on runtime class name if +className is not overriden
 *         string is build like MyClass -> my-class
 *
 *  @return NSString
 */
- (NSString *)buildChildPathByChildClass{
    const char* className = class_getName([self class]);
    
    Class class = [self class];
    
    NSString *overrideName = [class className];
    if(overrideName != nil){
        //DDLogDebug(@"%@ %@ -> Overridden: %@", THIS_FILE, THIS_METHOD, overrideName);
        return overrideName;
    }
    
    NSString *name = [NSString stringWithCString:className encoding:NSUTF8StringEncoding];
    
    NSString *snakeCaseString = [NSString stringWithSnakeCase:name];
    //DDLogDebug(@"%@ %@ -> Auth Generated: %@", THIS_FILE, THIS_METHOD, snakeCaseString);
    return snakeCaseString;
}

/**
 *  @brief Singleton manager
 *
 *  @return (SyncObserver*)id
 */
+ (instancetype)sharedManager{
    static SyncObserver *sharedManager = nil;
    static dispatch_once_t onceToken;
    dispatch_once(&onceToken, ^{
        sharedManager = [[self alloc] init];
    });
    return sharedManager;
}

/**
 *  @brief Remove all registered monitors
 */
- (void) removeAllRegisteredMonitors{
    
}

/**
 *  @brief Remove all registered monitors with complation block
 *
 *  @param complationBlock complationBlock(success)
 */
- (void) removeAllRegisteredMonitorsWithCompletionBlock:(_Nullable successCompletionBlock)complationBlock{
    
}

/**
 *  @brief Encode object as JSON and save it to firebase
 */
- (void) save{
    //NSDictionary *data = [[NSDictionary alloc] init];
    [self.childRefWithUID setValue:[NSDictionary dictionaryWithPropertiesOfObject:self] withCompletionBlock:^(NSError *error, Firebase *ref) {
        
    }];
}

- (void) saveWithAutoId{
    DDLogDebug(@"%@ %@", THIS_FILE, THIS_METHOD);
    [self.childRefWithUIDWithAutoId setValue:[NSDictionary dictionaryWithPropertiesOfObject:self] withCompletionBlock:^(NSError *error, Firebase *ref) {
        DDLogDebug(@"%@ %@ -> %@", error.description, ref, [NSDictionary dictionaryWithPropertiesOfObject:self]);
    }];
}

/**
 *  @brief Convinience method mainly to be overriden to save single piece of data
 *
 *  @param data
 */
- (void) save:(nonnull id)data{
    
}

/**
 *  @brief Save data to db by providing NSDictionary structure
 *
 *  @param data NSDictionary json like structure
 */
- (void) saveWithStructure:(nonnull id)data{
    __weak typeof(self) weakSelf = self;
    DDLogDebug(@"%@ %@ -> %@ with %@", THIS_FILE, THIS_METHOD, self.childRefWithUID, data);
    [self.childRefWithUID setValue:data withCompletionBlock:^(NSError *error, Firebase *ref) {
        DDLogError(@"%@ %@ -> %@", THIS_FILE, THIS_METHOD, error.description);
        
    }];
}

/**
 *  @brief Save data to db with auto id by providing NSDictionary structure
 *
 *  @param data NSDictionary json like structure
 */
- (void) saveWithAutoIdStructure:(nonnull id)data{
    __weak typeof(self) weakSelf = self;
    [self.childRefWithUIDWithAutoId setValue:data withCompletionBlock:^(NSError *error, Firebase *ref) {
        DDLogError(@"%@ %@ -> %@", THIS_FILE, THIS_METHOD, error.description);
    }];
}

/**
 *  @brief Save global/user independent data to db by providing NSDictionary structure
 *
 *  @param data NSDictionary json like structure
 */
- (void) saveGlobalWithStructure:(nonnull id)data{
    __weak typeof(self) weakSelf = self;
    [self.childRef setValue:data withCompletionBlock:^(NSError *error, Firebase *ref) {
        
    }];
}

/**
 *  @brief Save key value pair to db
 *
 *  @param key
 *  @param value
 */
- (void) saveWithKey:(nonnull NSString*)key withValue:(_Nullable id)value{
    [self.childRefWithUID setValue:@{key:value} withCompletionBlock:^(NSError *error, Firebase *ref) {
        
    }];
}

/**
 *  @brief Save key value pair to db by auto id
 *
 *  @param key
 *  @param value
 */
- (void) saveWithAutoIdWithChildKey:(nonnull NSString*)key withValue:(_Nullable id)value{
    [self.childRefWithUIDWithAutoId setValue:@{key:value} withCompletionBlock:^(NSError *error, Firebase *ref) {
        
    }];
}

/**
 *  @brief Save key value pair to db
 *
 *  @see saveWithKey:key:withValue
 *
 *  @param key        key
 *  @param value      value
 *  @param complation success block
 */
- (void) saveWithAutoIdWithChildKey:(nonnull NSString *)key withValue:(nonnull id)value withComplationBlock:(nullable successCompletionBlock)complation{
    [self.childRefWithUIDWithAutoId setValue:@{key:value} withCompletionBlock:^(NSError *error, Firebase *ref) {
        
    }];
}

#pragma mark - monitorSingleEventBy

/**
 *  @brief Monitor single changes, triggers @property singleEventCompletionBlock
 *
 *  @param dataEvent DataChange
 */
- (void) monitorSingleEventBy:(DataChange)dataEvent{
    [self.childRefWithUID observeSingleEventOfType:[self edc:dataEvent] withBlock:^(FDataSnapshot *snapshot) {
        
    }];
}

/**
 *  @brief Monitor single changes
 *
 *  @param dataEvent  DataChange
 *  @param completion completionBlock
 */
- (void) monitorSingleEventBy:(DataChange)dataEvent withCompletionBlock:(_Nullable completionBlock)completion{
    [self.childRefWithUID observeSingleEventOfType:[self edc:dataEvent] withBlock:^(FDataSnapshot *snapshot) {
        completion(snapshot);
    }];
}

/**
 *  @brief Monitor single changes
 *
 *  @param dataEvent  DataChange
 *  @param completion keysAndObjectsCompletionBlock
 */
- (void) monitorSingleEventBy:(DataChange)dataEvent withKeysAndObjectsCompletionBlock:(_Nullable keysAndObjectsCompletionBlock)completion{
    
}

#pragma mark - monitorContinuousEventBy

/**
 *  @brief Monitor continuous changes, triggers @property continuousEventCompletionBlock
 *
 *  @param dataEvent continuousEventCompletionBlock
 */
- (void) monitorContinuousEventBy:(DataChange)dataEvent{
    FirebaseHandle handle = [self.childRefWithUID observeEventType:[self edc:dataEvent] withBlock:^(FDataSnapshot *snapshot) {
        
    }];
    NSNumber *handleNo = [NSNumber numberWithUnsignedInteger:handle];
    [self.registeredMonitors addObject:handleNo];
}

/**
 *  @brief Monitor continuous changes
 *
 *  @param dataEvent  DataChange
 *  @param completion completionBlock
 */
- (void) monitorContinuousEventBy:(DataChange)dataEvent withCompletionBlock:(_Nullable completionBlock)completion{
    
}

/**
 *  @brief Monitor continuous changes
 *
 *  @param dataEvent  DataChange
 *  @param completion keysAndObjectsCompletionBlock
 */
- (void) monitorContinuousEventBy:(DataChange)dataEvent withKeysAndObjectsCompletionBlock:(_Nullable keysAndObjectsCompletionBlock)completion{
    
}

/**
 *  @brief Monitor continuous changes
 *
 *  @param dataEvent  DataChange
 *  @param completion completionBlockWithSnapshot
 */
- (void) monitorContinuousEventBy:(DataChange)dataEvent withSnapshotCompletionBlock:(nullable completionBlockWithSnapshot)completion{
    [self.childRefWithUID observeEventType:[self edc:dataEvent] withBlock:^(FDataSnapshot *snapshot) {
        completion(snapshot);
    }];
}

- (instancetype)constraintBy:(NSString *)constraint{
    return self;//[self.childRefWithUID ]
}

- (id)orderBy:(NSString *)order{
    self.query = [self.childRefWithUID queryOrderedByChild:order];
    return self;
}


+ (nullable NSString *)className{
    return nil;
}

- (FEventType)edc:(DataChange)data{
    return [self firebaseEventTypeWithDataChangeType:data];
}

- (FEventType)firebaseEventTypeWithDataChangeType:(DataChange)data{
    switch (data) {
        case DataAny:
            return FEventTypeValue;
            break;
        case DataAdded:
            return FEventTypeChildAdded;
            break;
        case DataMoved:
            return FEventTypeChildMoved;
            break;
        case DataChanged:
            return FEventTypeChildChanged;
            break;
        case DataRemoved:
            return FEventTypeChildRemoved;
            break;
            
        default:
            break;
    }
}

- (NSString*)description{
   // NSDictionary *properties = [[NSDictionary alloc] init];
    return [NSString stringWithFormat:@"%@", [NSDictionary dictionaryWithPropertiesOfObject:self]];
}


@end
